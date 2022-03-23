<?php

namespace app\admin\model;

use think\Model;

class PackageLog extends Model
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $insert = ['web' => 1];
}
