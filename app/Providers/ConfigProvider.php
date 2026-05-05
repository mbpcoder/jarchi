<?php
declare(strict_types=1);

namespace App\Providers;

use Dotenv\Dotenv;

class ConfigProvider
{
    private array $config = [];

    public function __construct()
    {
        $this->loadEnv();
        $this->loadConfigFiles();
    }


    private function loadEnv(): void
    {
        $dotenvPath = dirname(__DIR__);
        if (file_exists($dotenvPath . '/.env')) {
            $dotenv = Dotenv::createImmutable($dotenvPath);
            $dotenv->load();
            $dotenv->ifPresent('DEBUG')->isBoolean();
        }

        // Lazy-load config provider on first use
        $_CONFIG_PROVIDER = null;
    }

    private function loadConfigFiles(): void
    {
        $configDir = dirname(__DIR__, 2) . '/config';
        $configFiles = ['app.php', 'bot.php', 'webhooks.php'];

        foreach ($configFiles as $file) {
            $filePath = $configDir . '/' . $file;
            if (file_exists($filePath)) {
                $key = pathinfo($file, PATHINFO_FILENAME);
                $this->config[$key] = require $filePath;
            }
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $segments = explode('.', $key);
        $value = $this->config;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    public function has(string $key): bool
    {
        $segments = explode('.', $key);
        $value = $this->config;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return false;
            }
            $value = $value[$segment];
        }

        return true;
    }
}
