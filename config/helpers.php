<?php

declare(strict_types=1);

if (!function_exists('app_public_path')) {
    function app_public_path(): string
    {
        $scriptName = (string) ($_SERVER['SCRIPT_NAME'] ?? '');
        $basePath = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

        return $basePath === '/' ? '' : $basePath;
    }
}

if (!function_exists('app_index_url')) {
    function app_index_url(array $query = []): string
    {
        $url = app_public_path() . '/index.php';

        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        return $url;
    }
}

if (!function_exists('app_asset_url')) {
    function app_asset_url(string $assetPath): string
    {
        return app_public_path() . '/' . ltrim($assetPath, '/');
    }
}