<?php

namespace app\admin\model;

class Reptile extends Base
{
    protected $connection = [
        'prefix' => 'hc_',
    ];

    public $type = [
        1 => '国际金融'
    ];

    public $attribute = [
        1 => 'Facebook'
    ];
}