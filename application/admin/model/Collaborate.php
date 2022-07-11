<?php


namespace app\admin\model;


class Collaborate extends Base
{
    protected $connection = [
        'prefix' => 'jm_',
    ];

    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $insert = ['status' => 1, 'create_id', 'update_id', 'source' => 1, 'ip'];
    protected $update = ['update_id'];

    protected function setCreateIdAttr()
    {
        return getLoginUserId();
    }

    protected function setUpdateIdAttr()
    {
        return getLoginUserId();
    }

    protected function setIpAttr()
    {
        return request()->ip();
    }

}