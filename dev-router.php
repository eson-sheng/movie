<?php

declare(strict_types=1);

$path = (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

if (str_starts_with($path, '/api/')) {
    require __DIR__ . '/public/api.php';
    return true;
}

$file = __DIR__ . '/public' . rawurldecode($path);
if ($path !== '/' && is_file($file)) {
    return false;
}

require __DIR__ . '/public/index.php';
return true;
