<?php
declare(strict_types=1);

namespace App\Bots;

use InvalidArgumentException;
use App\Bots\Drivers\Base;
use App\Bots\Drivers\Telegram;
use App\Bots\Drivers\Bale;
use App\Bots\Drivers\RocketChat;

class BotManager
{
    protected array $drivers = [];

    public function driver(string|null $driver = null): Base
    {
        $driver = $driver ?: $this->getDefaultDriver();

        if (is_null($driver)) {
            throw new InvalidArgumentException(sprintf('Unable to resolve bot driver for [%s].', $driver));
        }

        if (!isset($this->drivers[$driver])) {
            $this->drivers[$driver] = $this->resolve($driver);
        }

        return $this->drivers[$driver];
    }

    public function resolveDriver(string|null $driver = null): Base
    {
        return $this->driver($driver);
    }

    protected function resolve(string $driver): Base
    {
        $config = $this->getConfig($driver);

        if (is_null($config)) {
            throw new InvalidArgumentException(sprintf('Bot driver [%s] is not defined.', $driver));
        }

        $driverMethod = 'create' . ucfirst($driver) . 'Driver';

        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($config);
        }

        throw new InvalidArgumentException(sprintf('Driver [%s] is not supported.', $driver));
    }

    protected function getConfig(string $driver): array|null
    {
        return config("bot.drivers.{$driver}");
    }

    public function getDefaultDriver(): string
    {
        return (string) config('bot.default_driver', 'telegram');
    }

    protected function createTelegramDriver(array $config): Base
    {
        return new Telegram($config);
    }

    protected function createBaleDriver(array $config): Base
    {
        return new Bale($config);
    }

    protected function createRocketchatDriver(array $config): Base
    {
        return new RocketChat($config);
    }

    public function __call(string $method, array $parameters)
    {
        return $this->driver()->{$method}(...$parameters);
    }
}
