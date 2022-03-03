<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class BannerContent extends Controller
{
    protected $model;
    protected function _initialize()
    {
        parent::_initialize();
        $this->model = model('BannerContent');
    }

    /**
     * 显示资源列表
     * @return \think\Response
     */
    public function index()
    {
        if(\request()->isPost()){
            $map = ['status'=>1];
            $data = $this->model->where($map)->order('id desc')->select();
            $count = $this->model->where($map)->count();
            return json(['data'=>['count'=>$count, 'list'=>$data]], 200);
        }
        return view();
    }

    /**
     * 显示创建资源表单页.
     * @return \think\Response
     */
    public function create()
    {
        if(Request()->isPost()) {
            $data = Request()->param();
            $data['create_id'] = getLoginUserId();
            $data['update_id'] = getLoginUserId();
            $state = $this->model->save($data);
            if($state !== false){
                return success_json();
            }
            return error_json();
        }
        return view();
    }

    /**
     * 显示编辑资源表单页.
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        if(Request()->isPost()) {
            $data = Request()->param();
            $data['update_id'] = getLoginUserId();
            $state = $this->model->save($data, ['id'=>$data['id']]);
            if($state !== false){
                return success_json(lang('EditSuccess', [lang('Bannel')]) );
            }
            return error_json();
        }
        $data = $this->model->find($id);
        return view('edit', ['data'=>$data]);
    }

    /**
     * 删除指定资源
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $id = \request()->param('id');
        $state = $this->model->save(['status'=>0,'update_id'=>getLoginUserId()], ['id'=>$id]);
        if($state !== false){
            return success_json(lang('EditSuccess', [lang('Bannel')]) );
        }
        return error_json();
    }
}
