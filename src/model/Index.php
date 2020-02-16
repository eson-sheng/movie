<?php
/**
 * Created by PhpStorm.
 * User: eson
 * Date: 2020-02-16
 * Time: 20:47
 */

namespace model;

use PDO;

class Index
{
    /*配置属性*/
    public $_config = [];
    /*数据库*/
    private $_db = null;

    /**
     * Index constructor.
     */
    public function __construct ()
    {
        $this->_config = require_once __DIR__ . '/../../config/database.php';
        $this->_db = new PDO(
            "mysql:host=localhost;dbname={$this->_config['database']};",
            $this->_config['username'],
            $this->_config['password'],
            array(
                /*错误异常模式处理*/
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                /*返回一个索引为结果集列名的数组*/
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                /*设置PDO属性预处理语句模拟*/
                PDO::ATTR_EMULATE_PREPARES => TRUE,
                /*初始化字符集*/
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
            )
        );

        $v = $this->_db->getAttribute(constant("PDO::ATTR_SERVER_VERSION"));
        dump($v);
    }
}