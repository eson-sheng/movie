<?php
/**
 * Created by PhpStorm.
 * User: eson
 * Date: 2020-02-25
 * Time: 01:54
 */

namespace validate;

use ResponseCode as ErrorCode;

class Danmaku extends Index
{
    public function checkoutAdd ($data)
    {
        if (empty($data['token'])) {
            $this->error(ErrorCode::TOKEN_ERROR);
            return false;
        }

        if (!$this->checkoutToken($data['token'])) {
            $this->error(ErrorCode::TOKEN_ERROR);
            return false;
        }

        if (empty($data['id'])) {
            $this->error(ErrorCode::VID_ERROR);
            return false;
        }

        if (strlen($data['id']) != 32) {
            $this->error(ErrorCode::VID_ERROR);
            return false;
        }

        if (empty($data['author'])) {
            $this->error(ErrorCode::VIDEO_AUTHOR_ERROR);
            return false;
        }

        if (empty($data['time'])) {
            $this->error(ErrorCode::VIDEO_TIME_ERROR);
            return false;
        }

        if (!$this->isFloat($data['time'])) {
            $this->error(ErrorCode::VIDEO_TIME_ERROR);
            return false;
        }

        if (empty($data['text'])) {
            $this->error(ErrorCode::VIDEO_AUTHOR_ERROR);
            return false;
        }

        if (empty($data['color'])) {
            $this->error(ErrorCode::VIDEO_DANMAKU_COLOR_ERROR);
            return false;
        }

        if (!is_integer($data['color'])) {
            $this->error(ErrorCode::VIDEO_DANMAKU_COLOR_ERROR);
            return false;
        }

        if (!is_integer($data['type'])) {
            $this->error(ErrorCode::VIDEO_TYPE_ERROR);
            return false;
        }

        return true;
    }
}