<?php

declare(strict_types=1);

namespace Config;

class AppConfig
{
    public function __construct(
        private array $settings
    ) {
    }

    public function appName(): string
    {
        return (string) ($this->settings['app']['name'] ?? 'Atelier Nova');
    }

    public function basePath(): string
    {
        return (string) ($this->settings['app']['base_path'] ?? '/public/index.php');
    }

    public function db(): array
    {
        return (array) ($this->settings['db'] ?? []);
    }
}