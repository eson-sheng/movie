<?php
/**
 * Created by PhpStorm.
 * User: eson
 * Date: 2020-02-24
 * Time: 16:21
 */

/**
 * @desc 输出视图层
 * @param $v string
 * @param $param array
 */
function view ($v, $param = [])
{
    /*获取html页面*/
    ob_start();
    /*页面参数*/
    extract($param);
    require_once __DIR__ . "/view/{$v}.html";
    $html = ob_get_contents();
    ob_end_clean();

    echo $html;
}