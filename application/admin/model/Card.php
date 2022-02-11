<?php

namespace app\admin\model;

use think\Model;

class Card extends Base
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
}
