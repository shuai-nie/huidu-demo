<?php
namespace app\admin\model;

class Firm extends Base
{
    protected $table = 'hc_firm';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
}