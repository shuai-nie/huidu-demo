<?php

namespace app\admin\model;

use think\Model;

class ResourceForm extends Model
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $insert = ['status' => 1];
}
