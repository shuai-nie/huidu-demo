<?php

namespace app\admin\controller;

use think\App;
use think\Loader;
use lib\Jurisdiction;
use think\Request;
use util\Tree;

class Group extends Base
{
    private $model;
    private $logic;

    function _initialize()
    {
        parent::_initialize();
        $this->model = Loader::model("Group");
        $this->logic = Loader::model('Group', 'logic');
    }

    public function index()
    {
        if (Request()->isPost()) {
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
            $this->logic->save_one(input('post.'));
        }
        $Jurisdiction = new Jurisdiction();
        $menuList     = $Jurisdiction->getAuthMenu(getLoginUserId(), 0);
        $info         = $this->logic->get_find($id);
        return view('', [
            'info'     => $info,
            'menuList' => $menuList,
        ]);
    }

    public function add()
    {
        if (request()->isPost()) {
            $this->logic->insert_one(input('post.'));
        }
        $Jurisdiction = new Jurisdiction();
//        $menuList = $Jurisdiction->getAuthMenu(getLoginUserId(), 0);
        return view('', [
//            'menuList' => $menuList,
        ]);
    }

    public function juri($id)
    {
        if (request()->isPost()) {
            $_post = request()->param();
            var_dump($_post);
            exit();
        }
        $AuthMenu = model('AuthMenu');
        $data = $AuthMenu->where(['show'=>1])->field('id,pid,title')->select();
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
        foreach ($data as $key=>$val){
            if(in_array($val['id'], $rules)) {
                $val['checked'] = true;
            }else {
                $val['checked'] = false;
            }
            $val['spread'] = true;
            $data[$key] = $val;
        }
        $data = Tree::toLayer($data);
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
            $this->logic->delete($id);
        }
    }
}