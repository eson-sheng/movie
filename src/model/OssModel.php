<?php
/**
 * Created by PhpStorm.
 * User: eson
 * Date: 2020-03-07
 * Time: 18:43
 */

namespace model;

use Redis;

/**
 * @desc 阿里云对象云存储 Class OssModel
 * @package model
 */
class OssModel
{
    /*配置属性*/
    private $_config = [];
    /*缓存redis方式*/
    public $redis = null;
    /*缓存时间*/
    public $cacheTime = 3600;

    public function __construct ()
    {
        $this->_config = config('oss.params');
        $redis_config = require_once __DIR__ . '/../../config/redis.php';
        $this->_config['redis.auth'] = $redis_config['redis.auth'];

        $this->redis = new Redis();
        $this->redis->connect('127.0.0.1', 6379);
        $this->redis->auth($this->_config['redis.auth']);
        $this->redis->select(1);
    }

    /**
     * @desc 302跳转oss资源文件链接
     * @param $file
     * @return bool
     */
    public function goToUrl ($file)
    {
        // 检查redis是否有键值
        $url = $this->getCache($file);
        if ($url) {
            Header("Location:$url");
            return true;
        }

        $bucket_name = $this->_config['bucket'];
        $expire = time() + $this->cacheTime;
        $StringToSign = "GET\n\n\n" . $expire . "\n/" . $bucket_name . "/" . $file;
        $Sign = base64_encode(
            hash_hmac(
                "sha1",
                $StringToSign,
                $this->_config['accessKeySecret'],
                true
            )
        );
        $sign_url_encode = urlencode($Sign);
        $path = urlencode($file);
        $url = "{$this->_config['endpoint']}{$path}?OSSAccessKeyId={$this->_config['accessKeyId']}&Expires={$expire}&Signature={$sign_url_encode}";

        // 存储redis
        $this->setCache($file, $url);

        Header("Location:$url");
        return true;
    }

    public function getCache ($key)
    {
        return $this->redis->get($key);
    }

    public function setCache ($key, $val)
    {
        return $this->redis->set($key, $val, $this->cacheTime);
    }
}