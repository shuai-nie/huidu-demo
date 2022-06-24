<?php
namespace app\admin\model;

class PackageHistory extends Base
{
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $insert = ['status' => 1];
}