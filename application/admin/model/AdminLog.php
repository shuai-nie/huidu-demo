<?php

namespace app\admin\model;

use think\Model;

class AdminLog extends Model
{
    protected $insert = ['ip'];
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
}