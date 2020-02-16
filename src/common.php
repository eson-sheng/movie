<?php
/**
 * Created by PhpStorm.
 * User: eson
 * Date: 2020-02-16
 * Time: 21:01
 */

/**
 * 获取页面字符串
 * @param $v string
 */
function view ($v)
{
    /*获取html页面*/
    ob_start();
    require_once __DIR__ . "/view/{$v}.html";
    $html = ob_get_contents();
    ob_end_clean();

    echo $html;
}