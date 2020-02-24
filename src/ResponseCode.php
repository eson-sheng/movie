<?php
/**
 * Created by PhpStorm.
 * User: eson
 * Date: 2020-02-25
 * Time: 01:53
 */

/**
 * @desc 错误码类
 * Class ResponseCode
 */
class ResponseCode
{
    const SUCCESS = 0;    // 成功

    /**
     * 令牌类 100
     */
    const TOKEN_ERROR = 110;   // 令牌错误

    /**
     * 视频资源类 800
     */
    const VID_ERROR = 801; // 视频ID错误
    const VIDEO_TIME_ERROR = 802; // 视频弹幕时间错误
    const VIDEO_AUTHOR_ERROR = 803; // 视频弹幕作者错误
    const VIDEO_CONTENT_ERROR = 804; // 视频弹幕内容错误
    const VIDEO_TYPE_ERROR = 805; // 视频弹幕类型错误
    const VIDEO_DANMAKU_COLOR_ERROR = 806; // 视频弹幕颜色错误
    const VIDEO_DANMAKU_INSERT_ERROR = 807; // 视频弹幕更新失败

    const CODE_MAP = [
        self::SUCCESS => 'OKAY',
        self::TOKEN_ERROR => '令牌错误',
        self::VID_ERROR => '视频ID错误或不存在',
        self::VIDEO_TIME_ERROR => '视频弹幕时间错误',
        self::VIDEO_AUTHOR_ERROR => '视频弹幕作者错误',
        self::VIDEO_CONTENT_ERROR => '视频弹幕内容错误',
        self::VIDEO_TYPE_ERROR => '视频弹幕类型错误',
        self::VIDEO_DANMAKU_COLOR_ERROR => '视频弹幕颜色错误',
        self::VIDEO_DANMAKU_INSERT_ERROR => '视频弹幕更新失败',
    ];
}