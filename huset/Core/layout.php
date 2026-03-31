<?php

declare(strict_types=1);

/**
 * Shared layout template for all pages.
 *
 * Usage in route files:
 *   $pageTitle = 'About';
 *   $pageDescription = 'Learn more about this demo.';
 *   ob_start();
 *   // ... your page content here ...
 *   $pageContent = ob_get_clean();
 *   require __DIR__ . '/../core/layout.php';
 *
 * Available variables:
 *   $pageTitle       - The page title (required)
 *   $pageDescription - Subtitle shown in header (optional)
 *   $pageContent     - The main content HTML (required)
 */

namespace Core;

// Ensure this file is part of the Core namespace
use function htmlspecialchars;

$pageTitle = $pageTitle ?? 'My Site';
$pageDescription = $pageDescription ?? '';
$pageContent = $pageContent ?? '';
$currentYear = date('Y');

// Load navigation from JSON
$navConfig = json_decode(file_get_contents(__DIR__ . '/../config/navigation.json'), true);
$navItems = $navConfig['items'] ?? [];

// Build breadcrumb from current URL path
$currentPath = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
$pathSegments = $currentPath === '' ? [] : explode('/', $currentPath);
$breadcrumbs = [['label' => 'Hjem', 'href' => '/']];

// Match segments to nav items for labels
$href = '';
foreach ($pathSegments as $i => $segment) {
    $href .= '/' . htmlspecialchars($segment);
    $label = ucfirst($segment);

    // Try to find a matching label in nav config
    foreach ($navItems as $item) {
        if ($i === 0 && $item['route'] === $segment) {
            $label = $item['label'];
            // Check children for deeper segments
            if (isset($item['children']) && isset($pathSegments[$i + 1])) {
                foreach ($item['children'] as $child) {
                    if ($child['route'] === $pathSegments[$i + 1]) {
                        // Parent label found; child will be added next iteration
                        break;
                    }
                }
            }
            break;
        }
        if ($i > 0 && isset($item['children'])) {
            foreach ($item['children'] as $child) {
                if ($child['route'] === $segment) {
                    $label = $child['label'];
                    break 2;
                }
            }
        }
    }

    $breadcrumbs[] = ['label' => $label, 'href' => $href];
}

?><!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?> — My Site</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="/js/app.js" defer></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 text-slate-900 font-sans flex flex-col">

    <!-- Navigation -->
    <header class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/70 backdrop-blur-xl">
        <div class="mx-auto max-w-5xl px-6 py-4 flex items-center justify-between">
            <!-- Burger button -->
            <button
                type="button"
                id="burger-btn"
                class="flex items-center gap-2 rounded-lg px-3 py-2 hover:bg-slate-100 transition-all"
                aria-label="Åpne meny"
                aria-expanded="false"
                aria-controls="mobile-menu">
                <svg class="h-6 w-6 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <span class="text-sm font-medium text-slate-700">Meny</span>
            </button>

            <a href="/" class="group flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-primary text-white font-bold text-sm shadow-sm">M</div>
                <span class="text-lg font-semibold text-slate-900 group-hover:text-primary transition-colors">My Site</span>
            </a>

            <!-- Desktop nav -->
            <nav class="md:flex items-center gap-1" aria-label="Hovednavigasjon">
                <?php foreach ($navItems as $i => $item): ?>
                    <div class="relative group">
                        <?php if (!empty($item['children'])): ?>
                            <div class="flex items-center relative">
                                <div class="flex items-center cursor-pointer submenu-toggle group px-1 py-1 rounded-lg hover:bg-slate-100 transition-all" aria-label="Vis undermeny" aria-expanded="false" data-menu="desktop-<?= $i ?>">
                                    <span class="mr-2 px-2 py-2 text-lg font-bold text-slate-600 group-hover:text-slate-900 select-none">+</span>
                                    <span class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 flex items-center gap-1 select-none">
                                        <?php echo htmlspecialchars($item['label']); ?>
                                    </span>
                                </div>
                                <ul class="absolute left-0 top-full mt-1 min-w-40 w-48 rounded-lg bg-white shadow-lg border border-slate-200 z-50 hidden submenu-list" id="desktop-<?= $i ?>">
                                    <?php foreach ($item['children'] as $child): ?>
                                        <li>
                                            <a class="block rounded-lg px-3 py-2 text-sm text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-all"
                                               href="<?php echo htmlspecialchars($child['href']); ?>">
                                                <?php echo htmlspecialchars($child['label']); ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php else: ?>
                            <a class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-all flex items-center gap-1"
                               href="<?php echo htmlspecialchars($item['href']); ?>">
                                <?php echo htmlspecialchars($item['label']); ?>
                            </a>
                        <?php endif; ?>
                        <!-- Submenu now rendered above inside parent div for floating effect -->
                    </div>
                <?php endforeach; ?>
            </nav>
        </div>

        <!-- Mobile menu -->
        <nav id="mobile-menu" class="fixed left-4 top-20 z-50 max-w-md w-[90vw] rounded-lg shadow-lg bg-white border border-slate-200/80" aria-label="Mobilnavigasjon">
            <ul class="px-4 py-3 space-y-1" role="list">
                <?php foreach ($navItems as $i => $item): ?>
                    <li>
                        <div class="flex items-center relative">
                            <?php if (!empty($item['children'])): ?>
                                <div class="flex items-center cursor-pointer submenu-toggle group px-1 py-1 rounded-lg hover:bg-slate-100 transition-all" aria-label="Vis undermeny" aria-expanded="false" data-menu="mobile-<?= $i ?>">
                                    <span class="mr-2 px-2 py-2 text-lg font-bold text-slate-600 group-hover:text-slate-900 select-none">+</span>
                                    <span class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 flex items-center gap-1 select-none">
                                        <?php echo htmlspecialchars($item['label']); ?>
                                    </span>
                                </div>
                                <ul class="absolute left-0 top-full mt-1 min-w-40 w-48 rounded-lg bg-white shadow-lg border border-slate-200 z-50 hidden submenu-list" id="mobile-<?= $i ?>">
                                    <?php foreach ($item['children'] as $child): ?>
                                        <li>
                                            <a class="block rounded-lg px-3 py-2 text-sm text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-all"
                                               href="<?php echo htmlspecialchars($child['href']); ?>">
                                                <?php echo htmlspecialchars($child['label']); ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <a class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-all"
                                   href="<?php echo htmlspecialchars($item['href']); ?>">
                                    <?php echo htmlspecialchars($item['label']); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </header>

    <!-- Breadcrumb -->
    <?php if (count($breadcrumbs) > 1): ?>
    <nav class="border-b border-slate-200/60 bg-white/40" aria-label="Brødsmuler">
        <div class="mx-auto max-w-5xl px-6 py-3">
            <ol class="flex items-center gap-1.5 text-sm text-slate-500" role="list">
                <?php foreach ($breadcrumbs as $i => $crumb): ?>
                    <?php $isLast = $i === count($breadcrumbs) - 1; ?>
                    <li class="flex items-center gap-1.5">
                        <?php if ($i > 0): ?>
                            <span aria-hidden="true" class="text-slate-300">/</span>
                        <?php endif; ?>
                        <?php if ($isLast): ?>
                            <span class="font-medium text-slate-900" aria-current="page"><?php echo htmlspecialchars($crumb['label']); ?></span>
                        <?php else: ?>
                            <a href="<?php echo htmlspecialchars($crumb['href']); ?>" class="hover:text-slate-900 transition-colors"><?php echo htmlspecialchars($crumb['label']); ?></a>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </nav>
    <?php endif; ?>

    <!-- Page Header -->
    <?php if ($pageTitle || $pageDescription): ?>
    <div class="border-b border-slate-200/60 bg-white/40">
        <div class="mx-auto max-w-5xl px-6 py-8">
            <h1 class="text-3xl font-bold tracking-tight text-slate-900"><?php echo htmlspecialchars($pageTitle); ?></h1>
            <?php if ($pageDescription): ?>
                <p class="mt-2 text-base text-slate-500"><?php echo htmlspecialchars($pageDescription); ?></p>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="flex-1">
        <div class="mx-auto max-w-5xl px-6 py-10">
            <?php echo $pageContent; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200/80 bg-white/50">
        <div class="mx-auto max-w-5xl px-6 py-6 flex items-center justify-between text-sm text-slate-400">
            <span>&copy; <?php echo $currentYear; ?> Mitt nettsted</span>
            <span>Bygget med PHP og Tailwind CSS</span>
        </div>
    </footer>

</body>
</html>
