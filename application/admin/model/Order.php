<?php
namespace app\admin\model;

class Order extends Base
{
    public $type = [
        ['id' => 0, 'title' => 'Vip订单'],
        ['id' => 1, 'title' => '置顶订单'],
    ];

    public $status = [
        ['id' => 0, 'title' => '未审核'],
        ['id' => 1, 'title' => '通过'],
        ['id' => 2, 'title' => '拒绝'],
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