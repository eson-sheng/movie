<?php
/**
 * Created by PhpStorm.
 * User: eson
 * Date: 2020-02-16
 * Time: 20:26
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
        return view(__FUNCTION__);
    }

    public function show ()
    {
        return view(__FUNCTION__);
    }
}