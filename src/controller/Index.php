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
        /**
         * @FIXME 视频播放页面
         */
        return view(__FUNCTION__);
    }
}