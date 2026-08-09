<?php

declare(strict_types=1);

$database = require dirname(__DIR__, 2) . '/config/database.php';

return [
    'database' => [
        'host' => getenv('MOVIE_DB_HOST') ?: '127.0.0.1',
        'port' => (int) (getenv('MOVIE_DB_PORT') ?: 3306),
        'name' => getenv('MOVIE_DB_NAME') ?: $database['database'],
        'username' => getenv('MOVIE_DB_USERNAME') ?: $database['username'],
        'password' => getenv('MOVIE_DB_PASSWORD') ?: $database['password'],
    ],
    'video_path' => dirname(__DIR__, 2) . '/public/video',
    'media_base_url' => '/video',
];
