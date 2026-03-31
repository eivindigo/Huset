<?php

declare(strict_types=1);

namespace Routes\User;

use Core\Http\StatusHandler;
use Core\Database\AuthQueries;
use Core\Database\Connection;
use function Core\Database\getUserByUsername;
use function Core\Database\incrementWrongPassword;
use function Core\Database\resetWrongPassword;
use function Core\Database\logLoginAttempt;
use function Core\Database\cleanupOldLogEntries;

session_start();

// Redirect if already logged in
if (!empty($_SESSION['user_id'])) {
    header('Location: /user');
    exit;
}

$pdo = Connection::get();
$error = '';
$fingerprint = 'IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown')
    . ' | UA: ' . ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown');

validateCsrfToken();

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($error !== '') {
    // CSRF failed — skip further processing
} elseif ($username === '' || $password === '') {
    $error = 'Brukernavn er påkrevd';
} else {
    $user = getUserByUsername($pdo, $username);

    if (!$user) {
        logLoginAttempt($pdo, $username, 'unknown_user', $fingerprint);
        $error = 'Ugyldige legitimasjon';
    } elseif ($user['lockout_manual']) {
        logLoginAttempt($pdo, $username, 'locked_manual', $fingerprint);
        $error = 'Kontoen er låst';
    } elseif (strtotime($user['lockout_timelimit']) > time()) {
        logLoginAttempt($pdo, $username, 'locked_timelimit', $fingerprint);
        $error = 'Midlertidig lås';
    } elseif (!$user['active']) {
        logLoginAttempt($pdo, $username, 'inactive', $fingerprint);
        $error = 'Kontoen er deaktivert.';
    } elseif (!password_verify($password, $user['password'])) {
        incrementWrongPassword($pdo, $user['user_id']);
        logLoginAttempt($pdo, $username, 'wrong_password', $fingerprint);
        $error = 'Ugyldige legitimasjon';
    } else {
        // Successful login
        resetWrongPassword($pdo, $user['user_id']);
        logLoginAttempt($pdo, $username, 'success');
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['display_name'] = $user['display_name'];

        // 10% chance: clean up log entries older than 3 months
        if (random_int(1, 10) === 1) {
            cleanupOldLogEntries($pdo);
        }

        header('Location: /user');
        exit;
    }
}

function loginUser($username, $password)
{
    $authQueries = new AuthQueries();
    $statusHandler = new StatusHandler();

    // ...existing code...
}

$pageTitle = 'Logg inn';
$pageDescription = '';
?>

<div
  class="mx-auto flex min-h-dvh w-full min-w-80 flex-col"
>
  <main class="flex max-w-full flex-auto flex-col">
    <div
      class="flex min-h-dvh flex-col bg-cover bg-bottom"
      style="background-image: url('https://cdn.tailkit.com/media/placeholders/photo-wQLAGv4_OYs-1920x1200.jpg');"
    >
      <section
        class="flex max-w-xl grow flex-col bg-white px-5 py-10 shadow-xl sm:px-20 sm:py-16"
      >
        <div class="flex grow items-center">
          <div class="grow space-y-10">
            <header>
              <h1 class="mb-2 inline-flex items-center gap-2 text-2xl font-bold">
                <svg
                  class="inline-block size-5 text-orange-600"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                  aria-hidden="true"
                >
                  <path
                    fill-rule="evenodd"
                    d="M9.638 1.093a.75.75 0 01.724 0l2 1.104a.75.75 0 11-.724 1.313L10 2.607l-1.638.903a.75.75 0 11-.724-1.313l2-1.104zM5.403 4.287a.75.75 0 01-.295 1.019l-.805.444.805.444a.75.75 0 01-.724 1.314L3.5 7.02v.73a.75.75 0 01-1.5 0v-2a.75.75 0 01.388-.657l1.996-1.1a.75.75 0 011.019.294zm9.194 0a.75.75 0 011.02-.295l1.995 1.101A.75.75 0 0118 5.75v2a.75.75 0 01-1.5 0v-.73l-.884.488a.75.75 0 11-.724-1.314l.806-.444-.806-.444a.75.75 0 01-.295-1.02zM7.343 8.284a.75.75 0 011.02-.294L10 8.893l1.638-.903a.75.75 0 11.724 1.313l-1.612.89v1.557a.75.75 0 01-1.5 0v-1.557l-1.612-.89a.75.75 0 01-.295-1.019zM2.75 11.5a.75.75 0 01.75.75v1.557l1.608.887a.75.75 0 01-.724 1.314l-1.996-1.101A.75.75 0 012 14.25v-2a.75.75 0 01.75-.75zm14.5 0a.75.75 0 01.75.75v2a.75.75 0 01-.388.657l-1.996 1.1a.75.75 0 11-.724-1.313l1.608-.887V12.25a.75.75 0 01.75-.75zm-7.25 4a.75.75 0 01.75.75v.73l.888-.49a.75.75 0 01.724 1.313l-2 1.104a.75.75 0 01-.724 0l-2-1.104a.75.75 0 11.724-1.313l.888.49v-.73a.75.75 0 01.75-.75z"
                    clip-rule="evenodd"
                  />
                </svg>
                <span>Logg inn</span>
              </h1>
              <h2 class="text-sm font-medium text-zinc-500">
                Skriv inn brukernavn og passord
              </h2>
            </header>

            <?php if ($error): ?>
              <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                <?php echo htmlspecialchars($error); ?>
              </div>
            <?php endif; ?>

            <form method="post" action="" class="space-y-6">
              <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
              <div class="space-y-1">
                <label for="username" class="inline-block text-sm font-medium">Brukernavn</label>
                <input
                  type="text"
                  id="username"
                  name="username"
                  placeholder="Skriv inn brukernavn"
                  required
                  autocomplete="username"
                  value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                  class="block w-full rounded-lg border border-zinc-200 px-5 py-3 leading-6 placeholder-zinc-500 focus:border-orange-500 focus:ring-3 focus:ring-orange-500/50"
                />
              </div>
              <div class="space-y-1">
                <label for="password" class="inline-block text-sm font-medium">Passord</label>
                <input
                  type="password"
                  id="password"
                  name="password"
                  placeholder="Skriv inn passord"
                  required
                  autocomplete="current-password"
                  class="block w-full rounded-lg border border-zinc-200 px-5 py-3 leading-6 placeholder-zinc-500 focus:border-orange-500 focus:ring-3 focus:ring-orange-500/50"
                />
              </div>
              <div>
                <button
                  type="submit"
                  class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-orange-700 bg-orange-700 px-6 py-3 leading-6 font-semibold text-white hover:border-orange-600 hover:bg-orange-600 hover:text-white focus:ring-3 focus:ring-orange-400/50 active:border-orange-700 active:bg-orange-700"
                >
                  <svg
                    class="inline-block size-5 opacity-50"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                  >
                    <path
                      fill-rule="evenodd"
                      d="M12.207 2.232a.75.75 0 00.025 1.06l4.146 3.958H6.375a5.375 5.375 0 000 10.75H9.25a.75.75 0 000-1.5H6.375a3.875 3.875 0 010-7.75h10.003l-4.146 3.957a.75.75 0 001.036 1.085l5.5-5.25a.75.75 0 000-1.085l-5.5-5.25a.75.75 0 00-1.06.025z"
                      clip-rule="evenodd"
                    />
                  </svg>
                  <span>Logg inn</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </section>
    </div>
  </main>
</div>

