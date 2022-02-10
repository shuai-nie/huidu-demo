<?php

namespace app\admin\model;

use think\Model;

class Resource extends Model
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
}
