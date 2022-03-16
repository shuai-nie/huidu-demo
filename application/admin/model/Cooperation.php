<?php

namespace app\admin\model;

use think\Model;

class Cooperation extends Base
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $insert = ['status' => 1, 'type_status' => 1, 'time'];

    protected function setTimeAttr()
    {
        return time();
    }
}
