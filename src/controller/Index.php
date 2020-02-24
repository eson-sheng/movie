<?php
/**
 * Created by PhpStorm.
 * User: eson
 * Date: 2020-02-24
 * Time: 16:04
 */

namespace controller;

class Index
{
    public function run ()
    {
        if (!empty($_REQUEST['N'])) {
            return $this->show();
        }

        return $this->index();
    }

    public function index ()
    {
        /**
         * @FIXME 列表数据
         */
        return view(__FUNCTION__);
    }

    public function show ()
    {
        /**
         * @FIXME 视频播放页面
         */
        return view(__FUNCTION__);
    }
}