<?php

namespace app\admin\controller;

use think\Loader;
use think\Log;

class Menu extends Base
{
    private $model;
    private $logic;

    function _initialize()
    {
        parent::_initialize();
        $this->model = Loader::model("AuthMenu");
        $this->logic = Loader::model('AuthMenu', 'logic');
    }

    public function index()
    {
        //var_dump(lang('EditSuccess', [lang('Bannel')]));exit();
        if (Request()->isPost()) {
            //$this->logic->getPageWithAdmin($this->page,$this->limit);exit;
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

    function add()
    {
        if (request()->isPost()) {
            $this->logic->add_one(input('post.'));
        }
        $menuList = $this->logic->get_all_menu(['pid' => 0]);
        return view('', [
            'menuList' => $menuList
        ]);
    }

    function edit($id = "")
    {
        if (request()->isPost()) {
            $data = input('post.');
            $id = request()->get('id');
            $state = model('AuthMenu')->update($data, ["id"=>$id]);
            if($state !== false ) {
               return success_json();
            }
            return error_json();
        }
        $info     = $this->logic->get_find($id);
        $menuList = $this->logic->get_all_menu(['pid' => 0]);
        return view('', [
            'info'     => $info,
            'menuList' => $menuList
        ]);
    }

    public function delete($id = "")
    {
        if ($id != "") {
            $this->logic->delete($id);
        }
    }
}
