<?php
declare(strict_types=1);

/**
 * Generate a random filename with optional prefix for temporary storage
 */
if (!function_exists('str_random')) {
    function str_random(string $prefix = 'payload'): string
    {
        return storage_path($prefix . '_' . substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 8) . '.json');
    }
}
