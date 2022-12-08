<?php

namespace app\admin\model;

use think\Model;

class UserInfo extends Base
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public static $iswed = [
        ['id' => 1, 'title' => '前台'],
        ['id' => 2, 'title' => '后台'],
        ['id' => 3, 'title' => 'H5'],
    ];
}
