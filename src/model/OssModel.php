<?php
/**
 * Created by PhpStorm.
 * User: eson
 * Date: 2020-03-07
 * Time: 18:43
 */

namespace model;


/**
 * @desc 阿里云对象云存储 Class OssModel
 * @package model
 */
class OssModel
{
    /*配置属性*/
    public $_config = [];

    public function __construct ()
    {
        $this->_config = config('oss.params');
    }

    /**
     * @desc 302跳转oss资源文件链接
     * @param $file
     * @return bool
     */
    public function goToUrl ($file)
    {
        $bucket_name = $this->_config['bucket'];
        $expire = time() + 3600;
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
        $url = "{$this->_config['endpoint']}{$path}?OSSAccessKeyId={$this->_config['accessKeyId']}&&Expires={$expire}&Signature={$sign_url_encode}";

        Header("Location:$url");
        return true;
    }
}