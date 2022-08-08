<?php
namespace app\admin\model;

class Subject extends Base
{
    public $type = [
        ['id' => 0, 'title' => '专题'],
        ['id' => 1, 'title' => '专区'],
    ];
    public $home_show = [
        ['id' => 0, 'title' => '否'],
        ['id' => 1, 'title' => '是'],
    ];

    protected $connection = [
        'prefix' => 'sj_',
    ];
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $insert = ['status' => 1, 'create_id', 'update_id'];
    protected $update = ['update_id'];

    protected function setCreateIdAttr()
    {
        return getLoginUserId();
    }

    protected function setUpdateIdAttr()
    {
        return getLoginUserId();
    }
}