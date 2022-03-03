<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class ContentCategory extends Controller
{
    protected $model;
    protected function _initialize()
    {
        parent::_initialize();
        $this->model = model('ContentCategory');
        $this->assign('meta_title', "文章分类");
    }

    /**
     * 显示资源列表
     * @return \think\Response
     */
    public function index()
    {
        if(\request()->isPost()){
            $map = ['is_del'=>1];
            $name = \request()->post('name');
            if(!empty($name)) {
                $map['name'] = ['like', "%{$name}%"];
            }
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
        $state = $this->model->save(['is_del'=>0,'update_id'=>getLoginUserId()], ['id'=>$id]);
        if($state !== false){
            return success_json(lang('EditSuccess', [lang('Bannel')]) );
        }
        return error_json();
    }
}
