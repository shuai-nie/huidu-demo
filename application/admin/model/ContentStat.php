<?php

namespace app\admin\model;

use think\Model;

class ContentStat extends Base
{
//    protected $table = 'jm_content_stat';
    protected $connection = [
        'prefix' => 'jm_',
    ];
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $insert = ['status' => 1, 'create_id', 'update_id'];
    protected $update = ['update_id'];

    protected function setCreateIdAttr()
    {
        return getLoginUserId();
    }

    protected function setUpdateIdAttr()
    {
        return getLoginUserId();
    }
}
