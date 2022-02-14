<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Banner extends Controller
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
            $adAll = model("Banner")->where($map)->order('id desc')->select();
            $count = model("Banner")->where($map)->count();
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
        if(Request()->isPost()) {
            $data = Request()->param();
            $data['create_id'] = getLoginUserId();
            $data['update_id'] = getLoginUserId();
            $state = model("Banner")->save($data);
            if($state !== false){
                return success_json();
            }
            return error_json();
        }
        return view();
    }


    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        if(Request()->isPost()) {
            $data = Request()->param();
            $data['update_id'] = getLoginUserId();
            $state = model("Banner")->save($data, ['id'=>$data['id']]);
            if($state !== false){
                return success_json(lang('EditSuccess', [lang('Bannel')]) );
            }
            return error_json();
        }
        $data = model("Banner")->find($id);
        return view('edit', ['data'=>$data]);
    }



    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $id = Request()->param('id');
        $state = model("Banner")->save(['status'=>0,'update_id'=>getLoginUserId()], ['id'=>$id]);
        if($state !== false){
            return success_json(lang('EditSuccess', [lang('Bannel')]) );
        }
        return error_json();
    }
}
