<?php

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable('..');
$dotenv->load();

$dotenv->ifPresent('DEBUG')->isBoolean();

function config(string $key, mixed $default = null)
{
    $result = $_ENV[$key] ?? $default;
    switch ($key) {
        case 'DEBUG':
            return $result === 'true';
    }
    return $result;
}