<?php

namespace app\admin\model;

use think\Model;

class UserConsume extends Model
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
}