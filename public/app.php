<?php

declare(strict_types=1);

use App\Application;
use App\Escaper;
use App\SchemaInitializer;
use App\UrlGenerator;
use Config\Database;
use Controllers\AuthController;
use Controllers\ContentController;
use Models\Post;
use Models\User;

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

spl_autoload_register(static function (string $class): void {
    $prefixes = [
        'App\\' => __DIR__ . '/../app/',
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

try {
    $pdo = Database::getInstance($config->db())->getConnection();
} catch (PDOException $exception) {
    http_response_code(500);
    echo '<h1>Database Error</h1>';
    echo '<p>Skontroluj DB nastavenia v config/config.php a import SQL skriptu.</p>';
    exit;
}

(new SchemaInitializer())->ensureInitialized($pdo);

$urlGenerator = new UrlGenerator();
$escaper = new Escaper();

$userModel = new User($pdo);
$userModel->createDefaultAdminIfNeeded();

$postModel = new Post($pdo);
$authController = new AuthController($urlGenerator, $escaper, $userModel);
$contentController = new ContentController($urlGenerator, $escaper, $postModel);

$application = new Application($authController, $contentController, $urlGenerator);
$application->run();
