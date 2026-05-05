<?php
declare(strict_types=1);

use App\Providers\ConfigProvider;

function storage_path(string $path = ''): string
{
    $storagePath = __DIR__ . '/../storage';

    if (!is_dir($storagePath)) {
        mkdir($storagePath, 0777, true);
    }

    $path = trim($path, '/\\');
    return $path === '' ? $storagePath : $storagePath . '/' . $path;
}

function str_random(string $prefix = 'payload'): string
{
    return storage_path($prefix . '_' . substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 8) . '.json');
}


/**
 * Get config value using dot notation
 *
 * Examples:
 * - config('app.debug')
 * - config('bot.default_driver')
 * - config('bot.drivers.telegram.token')
 * - config('webhooks.gitlab.chat_id')
 */
function config(string $key, mixed $default = null): mixed
{
    // Lazy-load config provider on first use
    $_CONFIG_PROVIDER = null;
    global $_CONFIG_PROVIDER;

    if ($_CONFIG_PROVIDER === null) {
        $_CONFIG_PROVIDER = new ConfigProvider();
    }

    return $_CONFIG_PROVIDER->get($key, $default);
}


/**
 * Get environment variable with optional default
 */
function env(string $key, mixed $default = null): mixed
{
    return $_ENV[$key] ?? $default;
}