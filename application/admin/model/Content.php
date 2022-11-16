<?php
namespace app\admin\model;

use think\Model;

class Content extends Base
{
    protected $connection = [
        'prefix' => 'jm_',
    ];
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
    protected $insert = ['status' => 1, 'create_id', 'update_id','home_sort'=>0,'home_top'=>0];
    protected $update = ['update_id'];

    public $isweb = [
        ['id'=>1, 'title'=>'前台'],
        ['id'=>2, 'title'=>'后台'],
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
