<?php
/**
 * Created by PhpStorm.
 * User: eson
 * Date: 2020-02-24
 * Time: 16:04
 */

namespace controller;

use model\Video;

class Index
{
    public function run ()
    {
        // 接口api
        if (!empty($_REQUEST['api'])) {

            // 添加弹幕
            if (empty($_GET['id'])) {
                return $this->add();
            }

            // 获取弹幕列表
            if (!empty($_GET['id'])) {
                return $this->showList();
            }

            // 默认返回
            return json();
        }

        // 视频播放页
        if (!empty($_REQUEST['N'])) {
            return $this->show();
        }

        // 视频列表页
        return $this->index();
    }

    /**
     * @desc 首页 - 视频列表页
     */
    public function index ()
    {
        $model = new Video();
        $list = $model->getVideoList();
        return view(__FUNCTION__, [
            'list' => $list,
        ]);
    }

    /**
     * @desc 视频播放页
     */
    public function show ()
    {
        $name = $_REQUEST['N'];

        $model = new Video();
        if (!$model->checkoutVideoName($name)) {
            return view('error');
        }

        return view(__FUNCTION__, [
            'id' => md5($name),
            'name' => $name,
            'api' => config('danmaku.api'),
            'token' => 'token',
            'user' => ip(),
        ]);
    }

    /**
     * @desc 添加弹幕接口
     * @return false|string
     */
    public function add ()
    {
        /**
         * TODO
         */
    }

    /**
     * @desc 获取视频弹幕接口
     * @return false|string
     */
    public function showList ()
    {
        /**
         * TODO
         */
    }
}