<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => $cookieParams['path'] ?? '/',
        'domain' => $cookieParams['domain'] ?? '',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

session_start();

require __DIR__ . '/../config/helpers.php';

spl_autoload_register(static function (string $class): void {
    $prefixes = [
        'Config\\' => __DIR__ . '/../config/',
        'Models\\' => __DIR__ . '/../models/',
        'Controllers\\' => __DIR__ . '/../controllers/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        $length = strlen($prefix);

        if (strncmp($prefix, $class, $length) !== 0) {
            continue;
        }

        $relativeClass = substr($class, $length);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

        if (file_exists($file)) {
            require $file;
        }
    }
});

$config = require __DIR__ . '/../config/config.php';

use Config\Database;
use Controllers\AuthController;
use Controllers\ContentController;
use Models\Post;
use Models\User;

try {
    $pdo = Database::getInstance($config['db'])->getConnection();
} catch (PDOException $exception) {
    http_response_code(500);
    echo '<h1>Database Error</h1>';
    echo '<p>Skontroluj DB nastavenia v config/config.php a import SQL skriptu.</p>';
    exit;
}

$userModel = new User($pdo);
$userModel->createDefaultAdminIfNeeded();

$postModel = new Post($pdo);
$authController = new AuthController($userModel);
$contentController = new ContentController($postModel);

$action = isset($_GET['action']) ? (string) $_GET['action'] : 'home';
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

switch ($action) {
    case 'home':
        $contentController->home();
        break;

    case 'admin':
        $contentController->dashboard();
        break;

    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController->login($_POST);
            break;
        }

        $authController->showLogin();
        break;

    case 'logout':
        $authController->logout();
        break;

    case 'post-create':
        $contentController->createForm();
        break;

    case 'post-store':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $contentController->store($_POST);
            break;
        }

        header('Location: ' . app_index_url(['action' => 'admin']));
        exit;

    case 'post-edit':
        if ($id > 0) {
            $contentController->editForm($id);
            break;
        }

        header('Location: ' . app_index_url(['action' => 'admin']));
        exit;

    case 'post-update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id > 0) {
            $contentController->update($id, $_POST);
            break;
        }

        header('Location: ' . app_index_url(['action' => 'admin']));
        exit;

    case 'post-delete':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id > 0) {
            $contentController->destroy($id, $_POST);
            break;
        }

        header('Location: ' . app_index_url(['action' => 'admin']));
        exit;

    case 'post-toggle-status':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id > 0) {
            $contentController->toggleStatus($id, $_POST);
            break;
        }

        http_response_code(405);
        echo 'Method not allowed';
        break;

    case 'post-toggle-featured':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id > 0) {
            $contentController->toggleFeatured($id, $_POST);
            break;
        }

        http_response_code(405);
        echo 'Method not allowed';
        break;

    default:
        header('Location: ' . app_index_url(['action' => 'home']));
        exit;
}
