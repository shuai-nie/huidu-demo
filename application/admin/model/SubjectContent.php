<?php
namespace app\admin\model;

class SubjectContent extends Base
{
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

    public $type = [
        ['id'=>0, 'title'=>'文章属性'],
        ['id'=>1, 'title'=>'文章ID'],
    ];

}