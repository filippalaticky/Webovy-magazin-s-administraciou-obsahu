<?php

declare(strict_types=1);

return new Config\AppConfig([
    'app' => [
        'name' => 'Atelier Nova',
        'base_path' => '/Webovy-magazin-s-administraciou-obsahu/public/index.php',
    ],
    'db' => [
        'host' => 'localhost',
        'port' => 3306,
        'database' => 'todo_app',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
    ],
]);

//http://localhost/Webovy-magazin-s-administraciou-obsahu/public/index.php?action=home