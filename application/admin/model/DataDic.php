<?php

namespace app\admin\model;

use think\Model;

class DataDic extends Base
{

    public function selectType($where)
    {
        return self::where($where)->order('sort desc')->select();
    }
}
