<?php
namespace app\admin\model;

class PackagePrice extends Base
{
    public $type = array(
        array('id'=>1, 'title'=>'月'),
        array('id'=>2, 'title'=>'季'),
        array('id'=>3, 'title'=>'年'),
    );

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