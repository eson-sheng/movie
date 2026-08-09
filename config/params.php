<?php

declare(strict_types=1);

$params = [
    'oss.params' => [
        'accessKeyId' => '',
        'accessKeySecret' => '',
        'endpoint' => '',
        'bucket' => '',
    ],
];

if (is_file(__DIR__ . '/local_params.php')) {
    $local_params = require __DIR__ . '/local_params.php';
    $params = array_merge($params, $local_params);
}

return $params;
