<?php

namespace app\admin\model;

use think\Model;

class RelationRecord extends Model
{
    public $operat = array(
        array('id'=>1, 'title'=>'未操作'),
        array('id'=>2, 'title'=>'联系方式有误'),
        array('id'=>3, 'title'=>'联系不上'),
        array('id'=>4, 'title'=>'已联系'),
    );
}
