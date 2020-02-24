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
}