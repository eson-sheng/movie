<?php
/**
 * Created by PhpStorm.
 * User: eson
 * Date: 2020-03-09
 * Time: 20:29
 */

$redis = [
    'redis.auth' => '',
];

if (is_file(__DIR__ . '/local_redis.php')) {
    $local_redis = require __DIR__ . '/local_redis.php';
    $redis = array_merge($redis, $local_redis);
}

return $redis;