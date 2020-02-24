<?php
/**
 * Created by PhpStorm.
 * User: eson
 * Date: 2020-02-25
 * Time: 01:52
 */

namespace validate;

use model\Token;
use ResponseCode as ErrorCode;

class Index
{
    public $error_code = 0;
    public $error_msg = '';

    function isFloat ($var)
    {
        if (is_numeric($var)) {
            if (strstr($var, '.')) {
                return true;
            }
        }

        return false;
    }

    public function error ($code)
    {
        $this->error_code = $code;
        $this->error_msg = ErrorCode::CODE_MAP[$code];
    }

    public function checkoutToken ($token)
    {
        $tokenModel = new Token();
        if (!$tokenModel->checkoutToken($token)) {
            return false;
        }
        return true;
    }
}