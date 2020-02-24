<?php
/**
 * Created by PhpStorm.
 * User: eson
 * Date: 2020-02-25
 * Time: 01:10
 */

$params = [
    'danmaku.api' => dirname("{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}") . '/?api=danmaku',
];

if (is_file(__DIR__ . '/local_params.php')) {
    $local_params = require_once __DIR__ . '/local_params.php';
    $params = array_merge($params, $local_params);
}

return $params;