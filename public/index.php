<?php
/**
 * Created by PhpStorm.
 * User: eson
 * Date: 2020-02-24
 * Time: 16:02
 */

require_once __DIR__ . "/../src/autoload.php";
require_once __DIR__ . "/../src/common.php";

use controller\Index;

$index = new Index();
$index->run();