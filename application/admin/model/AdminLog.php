<?php

namespace app\admin\model;

use think\Model;

class AdminLog extends Base
{
    protected $insert = ['ip'];
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
}
