<?php

declare(strict_types=1);

if (!function_exists('e')) {
    function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

$pageTitle = $pageTitle ?? 'To-Do List';
$layoutMode = $layoutMode ?? 'public';
$isLoggedIn = isset($_SESSION['user_id']);
$homeUrl = app_index_url(['action' => 'home']);
$adminUrl = app_index_url(['action' => 'admin']);
$loginUrl = app_index_url(['action' => 'login']);
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e((string) ($_SESSION['csrf_token'] ?? '')) ?>">
    <title><?= e($pageTitle) ?> | Atelier Nova</title>
    <link rel="stylesheet" href="<?= e(app_asset_url('css/style.css')) ?>">
    <script>
        window.APP_INDEX_URL = <?= json_encode(app_index_url(), JSON_UNESCAPED_SLASHES) ?>;
    </script>
</head>
<body>
<div class="bg-shape shape-one"></div>
<div class="bg-shape shape-two"></div>
<header class="site-header">
    <div class="container topbar topbar--spacious">
        <a class="brand" href="<?= e($homeUrl) ?>">
            <span class="brand-mark">A</span>
            <span class="brand-copy">
                <strong>Atelier Nova</strong>
                <small>editorial studio</small>
            </span>
        </a>

        <div class="user-panel">
            <?php if ($layoutMode === 'public'): ?>
                <?php if ($isLoggedIn): ?>
                    <span class="pill">Admin: <?= e((string) ($_SESSION['username'] ?? '')) ?></span>
                    <a class="btn btn-secondary" href="<?= e($adminUrl) ?>">Admin panel</a>
                    <a class="btn" href="<?= e(app_index_url(['action' => 'logout'])) ?>">Odhlasit</a>
                <?php else: ?>
                    <a class="btn btn-secondary" href="<?= e($adminUrl) ?>">Admin</a>
                <?php endif; ?>
            <?php else: ?>
                <a class="btn btn-secondary" href="<?= e($homeUrl) ?>">Verejna stranka</a>
                <?php if ($isLoggedIn): ?>
                    <span class="pill">Admin: <?= e((string) ($_SESSION['username'] ?? '')) ?></span>
                    <a class="btn" href="<?= e(app_index_url(['action' => 'logout'])) ?>">Odhlasit</a>
                <?php else: ?>
                    <a class="btn" href="<?= e($loginUrl) ?>">Prihlasit sa</a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</header>
<main class="container page-shell">
