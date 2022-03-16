<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class ContentHot extends Controller
{
    protected $model;
    protected $type = [
        0 => "热门文章",
//        1 => "热门精选",
    ];
    protected function _initialize()
    {
        parent::_initialize();
        $this->model = model('ContentHot');
        $this->assign('meta_title', "热门文章");

    }

    /**
     * 显示资源列表
     * @return \think\Response
     */
    public function index()
    {
        $ContentCategory = model('ContentCategory');
        if(\request()->isPost()){
            $map = ['A.status'=>1];
            $Content = model('Content');
            $limit  = \request()->post('limit');
            $page = \request()->post('page');
            $category_id = \request()->post('category_id');
            $type = \request()->post('type');
            $title = \request()->post('title');
            if(is_numeric($category_id)){
                $map['B.category_id'] = $category_id;
            }
            if(is_numeric($type)) {
                $map['A.type'] = $type;
            }
            if(!empty($title)) {
                $map['B.title'] = ['like', "%{$title}%"];
            }
            $offset = ($page - 1) * $limit;
            $data = $this->model
                ->alias('A')->join($Content->getTable().' B', "A.cid=B.id", "left")
                ->join($ContentCategory->getTable().' C', "B.category_id=C.id", "left")
                ->field('A.*,B.title,C.name as category_name')
                ->where($map)->order('A.id desc')->limit($offset, $limit)->select();
            $count = $this->model
                ->alias('A')->join($Content->getTable().' B', "A.cid=B.id", "left")
                ->where($map)->count();
            return json(['data'=>['count'=>$count, 'list'=>$data]], 200);
        }
        $category = $ContentCategory->where(['is_del'=>0])->field('id,name')->order("sort desc")->select();
        return view('', ['category' => $category, 'type' => $this->type]);
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
                return success_json(lang('CreateSuccess', [lang('ContentHot')]));
            }
            return error_json(lang('CreateSuccess', [lang('ContentHot')]));
        }
        $Content = model('Content');
        $ContentAll = $Content->where(['status'=>1])->field('id,title')->order('id desc')->select();
        return view('', [
            'content' => $ContentAll,
            'type' => $this->type
        ]);
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
                return success_json(lang('EditSuccess', [lang('ContentHot')]) );
            }
            return error_json(lang('EditFail', [lang('ContentHot')]));
        }
        $data = $this->model->find($id);
        $Content = model('Content');
        $ContentAll = $Content->where(['status'=>1])->field('id,title')->select();
        return view('edit', [
            'data' => $data,
            'content' => $ContentAll,
            'type' => $this->type
        ]);
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
            return success_json(lang('DeleteSuccess', [lang('ContentHot')]) );
        }
        return error_json(lang('DeleteFail', [lang('ContentHot')]) );
    }
}
