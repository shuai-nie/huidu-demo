<?php
namespace app\admin\model;

class Firm extends Base
{
    protected $table = 'hc_firm';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $insert = ['status' => 1, 'create_id', 'update_id', 'isweb' => 2];
    protected $update = ['update_id'];
    public $web_type = [
        1 => '页面',
        2 => '后台',
    ];

    protected function setCreateIdAttr()
    {
        return getLoginUserId();
    }

    protected function setUpdateIdAttr()
    {
        return getLoginUserId();
    }

}