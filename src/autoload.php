<?php
/**
 * Created by PhpStorm.
 * User: eson
 * Date: 2020-02-16
 * Time: 20:21
 */

/*自动加载*/
spl_autoload_register(function ($class) {
    require str_replace('\\', DIRECTORY_SEPARATOR, ltrim($class, '\\')) . '.php';
});