<?php
/**
 * Created by PhpStorm.
 * User: eson
 * Date: 2020-02-24
 * Time: 16:08
 */

$database = [
    'database' => '',
    'username' => 'root',
    'password' => '',
];

if (is_file(__DIR__ . '/local_database.php')) {
    $local_database = require_once __DIR__ . '/local_database.php';
    $database = array_merge($database, $local_database);
}

return $database;