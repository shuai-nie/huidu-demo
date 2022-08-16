<?php
namespace app\admin\model;

class Advert extends Base
{
    protected $connection = [
        'prefix' => 'mk_',
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

    public static function selectData($map = [])
    {
        $map['status'] = 1;
        return self::where($map)->select();
    }

    public static function allFind($id)
    {
        return self::where(['status' => 1, 'id'=>$id])->find();
    }


}