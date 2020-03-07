<?php
/**
 * Created by PhpStorm.
 * User: eson
 * Date: 2020-02-25
 * Time: 01:10
 */

$params = [
    'danmaku.api' => dirname("{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}") . '/?api=danmaku',
    'oss.params' => [
        'accessKeyId' => '',            // 访问密钥ID
        'accessKeySecret' => '',        // 访问密钥
        'endpoint' => '',               // 访问域名
        'bucket' => '',                 // 存储空间
    ],
];

if (is_file(__DIR__ . '/local_params.php')) {
    $local_params = require __DIR__ . '/local_params.php';
    $params = array_merge($params, $local_params);
}

return $params;