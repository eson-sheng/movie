<?php
/**
 * Created by PhpStorm.
 * User: eson
 * Date: 2020-02-24
 * Time: 16:04
 */

namespace controller;

use model\Index as IndexModel;

class Index
{
    public function run ()
    {
        $this->index();
    }

    public function index ()
    {
        $model = new IndexModel();
        $var = $model->v;

        return view(__FUNCTION__,[
            'var' => $var,
        ]);
    }
}