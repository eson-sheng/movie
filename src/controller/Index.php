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
        $model = new IndexModel();
        echo $model->v;
    }
}