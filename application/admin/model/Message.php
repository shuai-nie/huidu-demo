<?php
namespace app\admin\model;

class Message extends Base
{
    public $base_type = [
        ['id' => 1, 'title' => '系统消息'],
        ['id' => 2, 'title' => '活动消息'],
    ];
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $insert = ['status' => 1, 'create_id', 'update_id','source_type'=>1];
    protected $update = ['update_id'];

    protected function setCreateIdAttr()
    {
        return getLoginUserId();
    }

    protected function setUpdateIdAttr()
    {
        return getLoginUserId();
    }

    public function creData()
    {

    }



}