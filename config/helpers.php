<?php

declare(strict_types=1);

class Helpers
{
    public static function publicPath(): string
    {
        $scriptName = (string) ($_SERVER['SCRIPT_NAME'] ?? '');
        $basePath = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

        return $basePath === '/' ? '' : $basePath;
    }

    public static function indexUrl(array $query = []): string
    {
        $url = self::publicPath() . '/index.php';

        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        return $url;
    }

    public static function assetUrl(string $assetPath): string
    {
        return self::publicPath() . '/' . ltrim($assetPath, '/');
    }
}

if (!function_exists('app_public_path')) {
    function app_public_path(): string
    {
        return Helpers::publicPath();
    }
}

if (!function_exists('app_index_url')) {
    function app_index_url(array $query = []): string
    {
        return Helpers::indexUrl($query);
    }
}

if (!function_exists('app_asset_url')) {
    function app_asset_url(string $assetPath): string
    {
        return Helpers::assetUrl($assetPath);
    }
}