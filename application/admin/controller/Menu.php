<?php

namespace app\admin\controller;

use think\Loader;
use think\Log;
use util\Tree;

class Menu extends Base
{
    private $model;
    private $logic;

    function _initialize()
    {
        parent::_initialize();
        $this->model = model("AuthMenu");
        $this->logic = Loader::model('AuthMenu', 'logic');
    }

    public function index()
    {
        if (Request()->isPost()) {
            $map   = ['pid' => 0];
            $Menu  = $this->model->where($map)->select();
            $count = $this->model->where($map)->count();
            $data  = [
                'code' => 0,
                'msg'  => '',
                'data' => [
                    'count' => $count,
                    'list'  => $Menu
                ],
            ];
            return json($data);
        }
        return view();
    }

    public function read()
    {
        $map   = [];
        $Menu  = $this->model->where($map)->field('id,pid,title,link,sort,show')->select();
        return json(['code'=>0,'count'=>24,'data'=>$Menu], 200);

    }

    function add()
    {
        if (request()->isPost()) {
            $_post = request()->param();
            $state = model('AuthMenu')->save($_post);
            if($state !== false ) {
                return success_json();
            }
            return error_json();
        }
        $menuList = $this->logic->get_all_menu();
        return view('', [
            'menuList' => $menuList
        ]);
    }

    function edit($id = "")
    {
        if (request()->isPost()) {
            $data = request()->post();
            $id = request()->get('id');
            $state = model('AuthMenu')->save($data, ["id"=>$id]);
            if($state !== false ) {
               return success_json();
            }
            return error_json();
        }
        $info     = $this->logic->get_find($id);
        return view('', [
            'info'     => $info,
        ]);
    }

    public function delete($id = "")
    {
        if ($id != "") {
            $this->logic->delete($id);
        }
    }

    public function json()
    {
        Tree::config([
            'id'    => 'id',
            'pid'   => 'pid',
            'title' => 'title',
            'child' => 'children',
            'html'  => '┝ ',
            'step'  => 4,
        ]);

        $data = $this->model->where([])->select();
        if($data) {
            $data = collection($data)->toArray();
        }

        foreach ($data as  $key => $value) {
            $value['name'] = $value['title'];
            $value['open'] = true;
            $data[$key] = $value;
        }
        $data = Tree::toLayer($data);
        array_unshift($data, ['id' => 0, 'pid' => 0, 'name' => '顶级']);
        return json($data, 200);
    }
}
