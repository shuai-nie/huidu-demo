<?php

namespace app\admin\model;

use think\Model;

class Ad extends Base
{
    protected $table = 'hc_advertisement';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'createtime';
}
