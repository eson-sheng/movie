<?php
/**
 * Created by PhpStorm.
 * User: eson
 * Date: 2020-02-24
 * Time: 16:04
 */

namespace controller;

use model\Danmaku;
use model\Token;
use model\Video;
use ResponseCode as ErrorCode;
use validate\Danmaku as checkoutDanmaku;

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

        // token api
        if (!empty($_GET['s'])) {
            if ($_GET['s'] == 'token') {
                $tokenModel = new Token();
                $code = ErrorCode::SUCCESS;
                return json([
                    'message' => ErrorCode::CODE_MAP[$code],
                    'info' => $tokenModel->getToken(),
                ], $code);
            }
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
     * @desc 视频播放页 - mp4
     */
    public function show ()
    {
        $name = $_REQUEST['N'];
        $model = new Video();
        $tokenModel = new Token();
        $token = $tokenModel->getToken();

        if (!empty($_REQUEST['T'])) {
            if ($_REQUEST['T'] == 'hls') {
                return $this->_showHls($token);
            }
        }

        if (!$model->checkoutVideoName($name)) {
            return view('error');
        }

        return view(__FUNCTION__, [
            'json' => json_encode([
                'id' => md5($name),
                'name' => $name,
                'api' => config('danmaku.api'),
                'token' => $token,
                'user' => ip(),
                'url' => "./video/{$name}.mp4",
                'pic' => "./video/thum/{$name}.jpg",
            ]),
            'title' => $name,
        ]);
    }

    /**
     * @desc 视频播放页 - hls
     * @param $token
     */
    private function _showHls ($token)
    {
        $name = $_REQUEST['N'];
        $model = new Video();

        if (!$model->checkoutHlsVideo($name)) {
            return view('error');
        }

        $hlsVideoInfo = $model->getVideoHlsInfo($name);
        return view('show', [
            'json' => json_encode([
                'id' => $hlsVideoInfo['hash'],
                'name' => $hlsVideoInfo['name'],
                'api' => config('danmaku.api'),
                'token' => $token,
                'user' => ip(),
                'url' => "./video/hls/{$name}/index.m3u8",
                'pic' => "./video/hls/{$name}/index.png",
            ]),
            'title' => $hlsVideoInfo['name'],
        ]);
    }

    /**
     * @desc 添加弹幕接口
     * @return false|string
     */
    public function add ()
    {
        $params = json_decode(
            file_get_contents('php://input'),
            true
        );

        $checkoutDanmaku = new checkoutDanmaku();
        if (!$checkoutDanmaku->checkoutAdd($params)) {
            return json([
                'message' => $checkoutDanmaku->error_msg,
            ], $checkoutDanmaku->error_code);
        }

        $DanmakuModel = new Danmaku();
        $ret = $DanmakuModel->add($params);
        if (!$ret['status']) {
            $code = ErrorCode::VIDEO_DANMAKU_INSERT_ERROR;
            return json([
                'message' => [
                    ErrorCode::CODE_MAP[$code],
                    $ret['info'],
                ],
            ], $code);
        }

        $code = ErrorCode::SUCCESS;
        return json([
            'message' => ErrorCode::CODE_MAP[$code],
        ], $code);
    }

    /**
     * @desc 获取视频弹幕接口
     * @return false|string
     */
    public function showList ()
    {
        $checkoutDanmaku = new checkoutDanmaku();
        if (!$checkoutDanmaku->checkoutShowList($_GET)) {
            return json([
                'message' => $checkoutDanmaku->error_msg,
            ], $checkoutDanmaku->error_code);
        }

        $DanmakuModel = new Danmaku();
        $ret = $DanmakuModel->showList($_GET['id']);
        if (!$ret['status']) {
            $code = ErrorCode::VIDEO_DANMAKU_INSERT_ERROR;
            return json([
                'message' => [
                    ErrorCode::CODE_MAP[$code],
                    $ret['info'],
                ],
            ], $code);
        }

        $code = ErrorCode::SUCCESS;
        return json($ret['info'], $code);
    }
}