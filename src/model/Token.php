<?php
/**
 * Created by PhpStorm.
 * User: eson
 * Date: 2020-02-25
 * Time: 02:53
 */

namespace model;


class Token
{
    public function __construct ()
    {
        session_start();
    }

    public function getToken ()
    {
        $_SESSION['__hash__'] = md5(microtime(true));
        return $_SESSION['__hash__'];
    }

    public function checkoutToken ($hash)
    {
        if ($_SESSION['__hash__'] == $hash) {
            return $this->getToken();
        } else {
            return false;
        }
    }
}