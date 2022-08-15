<?php
namespace app\admin\model;

class Advert extends Base
{
    protected $connection = [
        'prefix' => 'mk_',
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