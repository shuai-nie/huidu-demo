<?php

namespace app\admin\model;

use think\Model;

class DataDic extends Base
{

    public function selectType($where, $field = '*')
    {
        return self::where($where)->order('sort desc')->field($field)->select();
    }

    public function findType($where)
    {
        return self::where($where)->find();
    }
}
