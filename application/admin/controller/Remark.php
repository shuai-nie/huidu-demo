<?php
namespace app\admin\controller;

use lib\Jurisdiction;
use think\Loader;
use think\View;

class Remark extends Base{

    public function index()
    {
        $list = model('AdminLog')->order('id desc')->limit(10)->select();
        return view('', ['list' => $list]);
    }
}