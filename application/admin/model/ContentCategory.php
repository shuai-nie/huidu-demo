<?php

namespace app\admin\model;

use think\Model;

class ContentCategory extends Base
{
    public $type = [
        1 =>['id'=>1, 'title'=>'文章分类'],
        2 => ['id'=>2, 'title'=>'文章属性'],
    ];
    protected $connection = [
        'prefix' => 'jm_',
    ];
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $insert = ['is_del' => 0, 'create_id', 'update_id'];
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
