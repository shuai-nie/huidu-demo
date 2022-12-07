<?php

namespace app\admin\model;

class NavigationBar extends Base
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'update_time';
    protected $updateTime = 'update_time';
    protected $insert = ['status' => 1];

    public static $link_type = [
        ['id'=>1, 'title'=>'站内'],
        ['id'=>2, 'title'=>'站外'],
    ];
}