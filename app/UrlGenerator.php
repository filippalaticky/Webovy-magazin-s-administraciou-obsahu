<?php

declare(strict_types=1);

namespace App;

class UrlGenerator
{
    public function publicPath(): string
    {
        $scriptName = (string) ($_SERVER['SCRIPT_NAME'] ?? '');
        $basePath = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

        return $basePath === '/' ? '' : $basePath;
    }

    public function indexUrl(array $query = []): string
    {
        $url = $this->publicPath() . '/index.php';

        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        return $url;
    }

    public function assetUrl(string $assetPath): string
    {
        return $this->publicPath() . '/' . ltrim($assetPath, '/');
    }
}