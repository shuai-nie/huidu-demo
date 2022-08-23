<?php

namespace app\admin\controller;

use think\Request;
use util\Tree;

class Group extends Base
{
    private $model;

    function _initialize()
    {
        parent::_initialize();
        $this->model = model("Group");
    }

    public function index()
    {
        if (Request()->isPost()) {
            $map = [];
            $data = model("Group")->where($map)->select();
            $count = model("Group")->where($map)->count();
            $data  = [
                'code' => 0,
                'msg'  => '',
                'data' => [
                    'count' => $count,
                    'list'  => $data
                ],
            ];
            return json($data);
        }
        return view();
    }

    public function edit($id)
    {
        if (request()->isPost()) {
            $_post = request()->param();
            $state = model('Group')->save(['group_name'=>$_post['group_name']], ['id'=>$id]);
            if($state !== false){
                return success_json(lang('EditSuccess', [lang('UserGroup')]));
            }
            return error_json(lang('EditFail', [lang('UserGroup')]));
        }
        $info = $this->model->find($id);
        return view('', [
            'info' => $info,
        ]);
    }

    public function add()
    {
        if (request()->isPost()) {
            $_post = request()->param();
            $state = model('Group')->save(['group_name'=>$_post['group_name']]);
            if($state !== false){
                return success_json(lang('CreateSuccess', [lang('UserGroup')]));
            }
            return error_json(lang('CreateFail', [lang('UserGroup')]));
        }
        return view('');
    }

    public function juri($id)
    {
        if (request()->isPost()) {
            $nodeIds = request()->post('nodeIds');
            $state = model('Group')->isUpdate(true)->save(['rules' => $nodeIds], ['id' => $id]);
            if ($state !== false) {
                return success_json();
            }
            return error_json();
        }

        $AuthMenu = model('AuthMenu');
        $data = $AuthMenu->where([])->field('id,pid,title')->select();
        if($data) {
            $data = collection($data)->toArray();
        }

        $GroupInfo = model('Group')->where(['id'=>$id])->find();
        Tree::config([
            'id'    => 'id',
            'pid'   => 'pid',
            'title' => 'title',
            'child' => 'children',
            'html'  => '┝ ',
            'step'  => 4,
        ]);
        $rules = explode(',', $GroupInfo->rules);
        $data = Tree::toLayer($data);
        foreach ($data as $k => $v) {
            if(!isset($v['children'])){
                if(in_array($v['id'], $rules) ) {
                    $data[$k]['checked'] = true;
                }
            }else{
                foreach ($data[$k]['children'] as $k1 => $v1){
                    if(!isset($v1['children'])){
                        if(in_array($v1['id'], $rules) ) {
                            $data[$k]['children'][$k1]['checked'] = true;
                        }
                    }else{
                        foreach ($data[$k]['children'][$k1]['children'] as $k2 => $v2){
                            if(in_array($v2['id'], $rules) ) {
                                $data[$k]['children'][$k1]['children'][$k2]['checked'] = true;
                            }
                        }
                    }
                }
            }
            $data[$k]['spread'] = true;
        }

        return view('', [
            'data'=> json_encode($data, JSON_UNESCAPED_UNICODE),
            'groupInfo'=>$GroupInfo
        ]);
    }

    /**
     * @param object|Request|null $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    public function add2()
    {
        return view('add2');
    }

    public function delete($id = "")
    {
        if ($id != "") {
            $state = model('Group')->where(['id'=>$id])->delete();
            if($state !== false){
                return success_json(lang('DeleteSuccess', [lang('UserGroup')]));
            }
            return error_json(lang('DeleteFail', [lang('UserGroup')]));
        }
    }
}