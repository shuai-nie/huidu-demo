<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Package extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        if(Request()->isPost()) {
            $map = ['status'=>1];
            $page = Request()->param('page');
            $limit = Request()->param('limit');
            $offset = ($page - 1) * $limit;
            $adAll = model("Package")->where($map)->limit($offset, $limit)->select();
            $count = model("Package")->where($map)->count();
            return json(['data'=>['count'=>$count, 'list'=>$adAll]], 200);
        }
        return view();
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        if(\request()->isPost()){
            $_post = \request()->param();
            $state = model("Package")->save($_post);
            if($state !== false){
                return success_json(lang('CreateSuccess', [lang('RESOURCE')]));
            }
            return error_json(lang('CreateFail', [lang('RESOURCE')]));
        }
        return view('');
    }



    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $Package = model("Package");
        if(\request()->isPost()){
            $_post = \request()->param();
            $state = $Package->save($_post, ['id'=>$id]);
            if($state !== false){
                return success_json(lang('EditSuccess', [lang('RESOURCE')]));
            }
            return error_json(lang('EditFail', [lang('RESOURCE')]));
        }
        $data = $Package->find($id);
        return view('', ['data'=>$data]);
    }



    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $Package = model("Package");
        if($id != ''){
            $_post = \request()->param();
            $state = $Package->save(['status'=>0], ['id'=>$id]);
            if($state !== false){
                return success_json(lang('DeleteSuccess', [lang('RESOURCE')]));
            }
            return error_json(lang('DeleteFail', [lang('RESOURCE')]));
        }
    }
}
