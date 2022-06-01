<?php

namespace app\admin\model;

use think\Model;

class CardContact extends Base
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $insert = ['status' => 1, 'creator', 'updator'];
    protected $update = ['update_id'];

    protected function setCreatorAttr()
    {
        return getLoginUserId();
    }

    protected function setUpdatorAttr()
    {
        return getLoginUserId();
    }
}
