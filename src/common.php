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

/**
 * @desc 获取配置文件
 * @param $key
 * @return mixed
 */
function config ($key)
{
    $config = require_once __DIR__ . '/../config/params.php';
    return $config[$key];
}

/**
 * 获取客户端IP地址
 * @access public
 * @param  integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param  boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function ip ($type = 0, $adv = true)
{
    $type = $type ? 1 : 0;
    static $ip = null;

    if (null !== $ip) {
        return $ip[$type];
    }

    if (!empty($_SERVER['X-REAL-IP'])) {
        $ip = $_SERVER['X-REAL-IP'];
    } elseif ($adv) {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos) {
                unset($arr[$pos]);
            }
            $ip = trim(current($arr));
        } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    // IP地址类型
    $ip_mode = (strpos($ip, ':') === false) ? 'ipv4' : 'ipv6';

    // IP地址合法验证
    if (filter_var($ip, FILTER_VALIDATE_IP) !== $ip) {
        $ip = ('ipv4' === $ip_mode) ? '0.0.0.0' : '::';
    }

    // 如果是ipv4地址，则直接使用ip2long返回int类型ip；如果是ipv6地址，暂时不支持，直接返回0
    $long_ip = ('ipv4' === $ip_mode) ? sprintf("%u", ip2long($ip)) : 0;

    $ip = [$ip, $long_ip];

    return $ip[$type];
}

/**
 * @param array $data
 * @param int $code
 */
function json ($data = [], $code = 0)
{
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode([
        'code' => $code,
        'data' => $data
    ]);
}
