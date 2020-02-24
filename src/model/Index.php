<?php
/**
 * Created by PhpStorm.
 * User: eson
 * Date: 2020-02-24
 * Time: 16:06
 */

namespace model;

use PDO;

class Index
{
    /*配置属性*/
    public $_config = [];
    /*数据库*/
    public $_db = null;
    /*数据库版本*/
    public $v = null;

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

        $this->v = $this->_db->getAttribute(
            constant("PDO::ATTR_SERVER_VERSION")
        );
    }

    /**
     * @desc 新增数据
     * @param $table
     * @param $columns
     * @return array [status => bool,info => rowCount/error]
     */
    public function insert ($table, $columns)
    {
        $keys = [];
        foreach (array_keys($columns) as $key => $value) {
            if ($value === '*' || strpos($value, '.') !== false || strpos($value, '`') !== false) {
                //不用做处理
            } elseif (strpos($value, '`') === false) {
                $value = '`' . trim($value) . '`';
            }
            $keys[$key] = $value;
        }

        $placeholder = ":" . join(',:', array_keys($columns));
        $fieldsStr = join(',', $keys);
        $sql = "INSERT {$table}({$fieldsStr}) VALUES({$placeholder})";
        $stmt = $this->_db->prepare($sql);

        try {
            $this->_db->beginTransaction();
            $ret['status'] = $stmt->execute($columns);
            $ret['info'] = $stmt->rowCount();
            $ret['lastID'] = $this->_db->lastInsertId();
            $this->_db->commit();
            return $ret;
        } catch (\Exception $e) {
            $this->_db->rollback();
            return [
                'status' => false,
                'info' => $e->getMessage(),
            ];
        }
    }
}