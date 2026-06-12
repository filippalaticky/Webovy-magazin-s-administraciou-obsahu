<?php

declare(strict_types=1);

use Config\Database;

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
} catch (PDOException $e) {
    echo '<h1>DB connection failed</h1>';
    echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    exit;
}

echo '<h1>DB connected</h1>';

try {
    $stmt = $pdo->query('SHOW TABLES');
    $tables = $stmt->fetchAll(PDO::FETCH_NUM);

    echo '<h2>Tables</h2>';
    if (empty($tables)) {
        echo '<p>No tables found in database.</p>';
    } else {
        echo '<ul>';
        foreach ($tables as $t) {
            echo '<li>' . htmlspecialchars($t[0]) . '</li>';
        }
        echo '</ul>';
    }

    echo '<h2>Users count</h2>';
    try {
        $row = $pdo->query('SELECT COUNT(*) AS c FROM users')->fetch(PDO::FETCH_ASSOC);
        echo '<p>' . ((int) ($row['c'] ?? 0)) . ' users</p>';
    } catch (PDOException $e) {
        echo '<p>Error checking users table: ' . htmlspecialchars($e->getMessage()) . '</p>';
    }
} catch (PDOException $e) {
    echo '<p>Error listing tables: ' . htmlspecialchars($e->getMessage()) . '</p>';
}

