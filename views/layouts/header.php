<?php

declare(strict_types=1);

$pageTitle = $pageTitle ?? 'To-Do List';
$layoutMode = $layoutMode ?? 'public';
$isLoggedIn = isset($_SESSION['user_id']);
$homeUrl = $urlGenerator->indexUrl(['action' => 'home']);
$adminUrl = $urlGenerator->indexUrl(['action' => 'admin']);
$loginUrl = $urlGenerator->indexUrl(['action' => 'login']);
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= $escaper->escape((string) ($_SESSION['csrf_token'] ?? '')) ?>">
    <title><?= $escaper->escape($pageTitle) ?> | Atelier Nova</title>
    <link rel="stylesheet" href="<?= $escaper->escape($urlGenerator->assetUrl('css/style.css')) ?>">
    <script>
        window.APP_INDEX_URL = <?= json_encode($urlGenerator->indexUrl(), JSON_UNESCAPED_SLASHES) ?>;
    </script>
</head>
<body>
<div class="bg-shape shape-one"></div>
<div class="bg-shape shape-two"></div>
<header class="site-header">
    <div class="container topbar topbar--spacious">
        <a class="brand" href="<?= $escaper->escape($homeUrl) ?>">
            <span class="brand-mark">A</span>
            <span class="brand-copy">
                <strong>Atelier Nova</strong>
                <small>editorial studio</small>
            </span>
        </a>

        <div class="user-panel">
            <?php if ($layoutMode === 'public'): ?>
                <?php if ($isLoggedIn): ?>
                    <span class="pill">Admin: <?= $escaper->escape((string) ($_SESSION['username'] ?? '')) ?></span>
                    <a class="btn btn-secondary" href="<?= $escaper->escape($adminUrl) ?>">Admin panel</a>
                    <a class="btn" href="<?= $escaper->escape($urlGenerator->indexUrl(['action' => 'logout'])) ?>">Odhlasit</a>
                <?php else: ?>
                    <a class="btn btn-secondary" href="<?= $escaper->escape($adminUrl) ?>">Admin</a>
                <?php endif; ?>
            <?php else: ?>
                <a class="btn btn-secondary" href="<?= $escaper->escape($homeUrl) ?>">Verejna stranka</a>
                <?php if ($isLoggedIn): ?>
                    <span class="pill">Admin: <?= $escaper->escape((string) ($_SESSION['username'] ?? '')) ?></span>
                    <a class="btn" href="<?= $escaper->escape($urlGenerator->indexUrl(['action' => 'logout'])) ?>">Odhlasit</a>
                <?php else: ?>
                    <a class="btn" href="<?= $escaper->escape($loginUrl) ?>">Prihlasit sa</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</header>
<main class="container page-shell">
