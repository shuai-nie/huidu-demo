<?php

namespace app\admin\model;

use think\Model;

class Complaint extends Base
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'dispose_time';
}
