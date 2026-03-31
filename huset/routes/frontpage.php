<?php
declare(strict_types=1);

namespace Routes;
require_once __DIR__ . '/../init.php';

// Start output buffering
ob_start();

// Frontpage route

$pageTitle = 'Velkommen';
$pageDescription = 'En liten PHP-ruter med Tailwind-stil.';

?>

<!-- Hero -->
<section class="text-center py-8">
    <h2 class="text-5xl font-bold tracking-tight bg-gradient-to-r from-primary to-amber-500 bg-clip-text text-transparent">
        Velkommen
    </h2>
    <p class="mt-4 text-lg text-slate-500 max-w-2xl mx-auto">
        En lettvekts PHP-ruter med parameteriserte ruter, sikkerhetsfunksjoner og pen styling.
    </p>
</section>


<?php

// Capture the content and include the layout
$pageContent = ob_get_clean();
require_once __DIR__ . '/../Core/layout.php';

