<?php

namespace app\admin\model;

use think\Model;

class BannerContent extends Base
{
//    protected $table = 'jm_banner_content';
    protected $connection = [
        // 数据库表前缀
        'prefix'      => 'jm_',
    ];

    protected $autoWriteTimestamp = true;
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
