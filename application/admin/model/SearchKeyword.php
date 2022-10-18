<?php
namespace app\admin\model;

class SearchKeyword extends Base
{
    protected $connection = [
        'prefix' => 'mk_',
    ];

    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';

    public $type = [
        0 => '综合',
        1 => '合作',
        2 => '需求',
        3 => '咨询',
        4 => '人脉',
    ];

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