<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Content extends Controller
{
    protected $model;
    protected function _initialize()
    {
        parent::_initialize();
        $this->model = model('Content');
        $this->assign('meta_title', "文章管理");
    }

    /**
     * 显示资源列表
     * @return \think\Response
     */
    public function index()
    {
        if(\request()->isPost()){
            $map = ['status'=>1];
            $limit = \request()->post('limit');
            $page = \request()->post('page');
            $offset = ($page - 1) * $limit;
            $data = $this->model->where($map)->limit($offset, $limit)->order('id desc')->select();
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
                return success_json(lang('CreateSuccess', [lang('Content')]));
            }
            return error_json(lang('CreateFail', [lang('Content')]));
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
                return success_json(lang('EditSuccess', [lang('Content')]) );
            }
            return error_json(lang('EditFail', [lang('Content')]));
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
            return success_json(lang('DeleteSuccess', [lang('Content')]) );
        }
        return error_json(lang('DeleteFail', [lang('Content')]));
    }
}
