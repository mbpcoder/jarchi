<?php
declare(strict_types=1);

return [
    'debug' => env('DEBUG', false) === 'true' || env('DEBUG', false) === true,
];
