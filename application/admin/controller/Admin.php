<?php

namespace app\admin\controller;

use think\Loader;

class Admin extends Base
{

    function _initialize()
    {
        parent::_initialize();
    }

    // 主页
    public function index()
    {
        if (request()->isPost()) {
            $map      = ['status'=>1];
            $userData = model("Admin")->where($map)->select();
            $count    = model("Admin")->where($map)->count();
            $data     = [
                'code' => 0,
                'msg'  => '',
                'data' => [
                    'count' => $count,
                    'list'  => $userData,
                ],
            ];
            return json($data);

        }
        return view();
    }

    // 编辑
    public function edit($id)
    {
        $Admin = model('Admin');
        $info = $Admin->find($id);
        if (request()->isPost()) {
            $_post = request()->param();
            if(!empty($_post['password'])){
                $_post['password'] = md5(md5($_post['password']).$info['str']);
            }else{
                unset($_post['password']);
            }
            $state = $Admin->save($_post, ['id'=>$id]);
            if($state !== false){
                return success_json(lang('EditSuccess', [lang('User')]));
            }
            return error_json(lang('EditFail', [lang('User')]));
        }
        $AllGroup = model("Group")->where([])->field('id,group_name')->select();
        return view('', [
            'groupList' => $AllGroup,
            'info'      => $info,
        ]);
    }

    /*
    新增
    */
    function add()
    {
        if (request()->isPost()) {
            $_post = request()->param();
            $number = GetRandStr(12);
            $_post['password'] = md5(md5($_post['password']) . $number);
            $_post['str'] = $number;
            $state = model('Admin')->save($_post);
            if($state !== false){
                return success_json(lang('CreateSuccess', [lang('User')]));
            }
            return error_json(lang('CreateFail', [lang('User')]));
        }
        $AllGroup = model("Group")->where([])->field('id,group_name')->select();
        return view('', [
            'group' => $AllGroup,
        ]);
    }

    /*删除*/
    function delete($id = "")
    {
        if ($id != "") {
            $state = model('Admin')->save(['status'=>0], ['id'=>$id]);
            if($state !== false){
                return success_json(lang('DeleteSuccess', [lang('User')]));
            }
            return error_json(lang('DeleteFail', [lang('User')]));
        }
    }
}