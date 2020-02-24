<?php
/**
 * Created by PhpStorm.
 * User: eson
 * Date: 2020-02-25
 * Time: 02:38
 */

namespace model;


class Danmaku extends Index
{
    public function add ($data)
    {
        return $this->insert('danmaku', [
            'vid' => $data['id'],
            'author' => $data['author'],
            'time' => $data['time'],
            'content' => $data['text'],
            'color' => $data['color'],
            'type' => $data['type'],
            'ip' => ip(),
            'referer' => $_SERVER['HTTP_REFERER'] ?: '',
            'equipment' => $_SERVER['HTTP_USER_AGENT'] ?: '',
        ]);
    }

    public function showList ($vid)
    {
        $stmt = $this->_db->prepare("
            SELECT 
                `time`,`type`,`color`,`author`,`content` 
            FROM 
                `danmaku` 
            WHERE
                vid = :vid;
        ");

        try {
            $this->_db->beginTransaction();
            $ret['status'] = $stmt->execute([
                'vid' => $vid,
            ]);
            $ret['info'] = $this->showListByDealWith(
                $stmt->fetchAll(\PDO::FETCH_NUM)
            );
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

    public function showListByDealWith ($items)
    {
        foreach ($items as $key => $value) {
            for ($i = 0; $i < 3; $i++) {
                $items[$key][$i] = floatval($value[$i]);
            }
        }
        return $items;
    }
}