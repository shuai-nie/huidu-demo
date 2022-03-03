<?php

namespace app\admin\model;

use think\Model;

class ContentCategory extends Base
{
    protected $table = 'jm_content_category';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $insert = ['is_del' => 1, 'create_id', 'update_id'];
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
