<?php
namespace app\admin\controller;

use app\admin\model\Config;

class SafeIp extends Base
{

    public function index()
    {
        $map = ['id'=>101];
        if(request()->isPost()){
            $_post = request()->post();
            $state = Config::update(['value'=>$_post['value']], $map);
            if($state != false){
                return success_json('修改成功');
            }
            return error_json('修改失败');
        }
        $list = Config::where($map)->find();
        return view('', ['list' => $list, 'meta_title' => '黑名单']);
    }

}