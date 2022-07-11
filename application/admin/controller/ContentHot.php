<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class ContentHot extends Base
{
    protected $model;
    protected $type = [
        0 => "热门文章",
//        1 => "热门精选",
    ];
    public function _initialize()
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
                $map['A.category_id'] = $category_id;
            }
            if(!empty($title)) {
                $map['A.title'] = ['like', "%{$title}%"];
            }
            $offset = ($page - 1) * $limit;
            $data = $Content->alias('A')
                ->join($ContentCategory->getTable().' C', "A.category_id=C.id", "left")
                ->field('A.*,C.name as category_name')
                ->where($map)->order('A.id desc')->limit($offset, $limit)->select();
            $count = $Content->alias('A')
                ->join($ContentCategory->getTable().' C', "A.category_id=C.id", "left")
                ->where($map)->count();

            foreach ($data as $k => $v) {
                $type0 = $this->model->where(['cid' => $v['id'], 'type' => 0])->find();
                if($type0){
                    $v['type0'] = $type0['status'];
                    $v['sort0'] = $type0['sort'];
                }

                $type1 = $this->model->where(['cid' => $v['id'], 'type' => 1])->find();
                if($type1){
                    $v['type1'] = $type1['status'];
                    $v['sort1'] = $type1['sort'];
                }

                $type2 = $this->model->where(['cid' => $v['id'],'type' => 2])->find();
                if($type2){
                    $v['type2'] = $type2['status'];
                    $v['sort2'] = $type2['sort'];
                }

                $data[$k] = $v;
            }

            return json(['data'=>['count'=>$count, 'list'=>$data]], 200);
        }
        $category = $ContentCategory->where(['is_del'=>0])->field('id,name')->order("sort desc")->select();
        return view('', ['category' => $category, 'type' => $this->type]);
    }

    public function topsort()
    {
        if(\request()->isPost()) {
            $id = \request()->post('id');
            $name = \request()->post('name');
            $value = \request()->post('value');
            $type = mb_substr($name, strlen($name)-1, count($name));
            $count = $this->model->where(['cid' => $id, 'type'=>$type])->count();
            $name = mb_substr($name, 0, strlen($name)-1);
            if($count > 0) {
                $state = $this->model->save([$name=>$value], ['cid'=>$id, 'type'=>$type]);
            }else{
                $state = $this->model->save([
                    $name => $value,
                    'cid' => $id,
                    'type' => $type,
                ]);
            }

            if($state !== false ){
                return success_json("修改成功");
            }
            return error_json("修改失败");

        }

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
