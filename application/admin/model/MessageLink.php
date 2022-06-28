<?php
namespace app\admin\model;

class MessageLink extends Base
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public $link_type = [
//        ['id' => 1, 'title' => '消息封面'],
//        ['id' => 2, 'title' => '消息ICON'],
        ['id' => 3, 'title' => '详情链接'],
        ['id' => 4, 'title' => '联系客服'],
    ];

}