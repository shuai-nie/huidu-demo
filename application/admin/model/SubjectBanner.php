<?php
namespace app\admin\model;

class SubjectBanner extends Base
{
    protected $connection = [
        'prefix' => 'sj_',
    ];
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $insert = ['status' => 1, 'create_id', 'update_id'];
    protected $update = ['update_id'];
    public $type  = [
        0 => ['id'=>0, 'title'=>'专题入口'],
        1 => ['id'=>1, 'title'=>'首页入口'],
        2 => ['id'=>2, 'title'=>'内页'],
    ];

    protected function setCreateIdAttr()
    {
        return getLoginUserId();
    }

    protected function setUpdateIdAttr()
    {
        return getLoginUserId();
    }

}