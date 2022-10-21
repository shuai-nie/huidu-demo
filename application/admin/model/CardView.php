<?php

namespace app\admin\model;

use think\Model;

class CardView extends Model
{
    public $type = [
        1 => '资源查看',
        2 => '人脉圈查看',
    ];
}
