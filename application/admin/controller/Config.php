<?php

namespace app\admin\controller;

use think\Db;
use think\Loader;

class Config extends Base
{
    function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        if (request()->isPost()) {
            $page  = request()->param('page');
            $limit = request()->param('limit');
            $offset = ($page - 1) * $limit ;
            $map   = [];
            $list  = model('Config')->where($map)->limit($offset, $limit)->order('id desc')->select();
            $count = model('Config')->where($map)->count();
            return json(['data' => ['count' => $count, 'list' => $list]], 200);
        }
        return view();
    }

    public function add()
    {
        if (request()->isPost()) {
            $params = request()->param();
            $state = model('Config')->save($params);
            if ($state !== false) {
                return success_json();
            }
            return error_json();
        }
        return view();
    }

    public function edit($id)
    {
        if (request()->isPost()) {
            $params= request()->param();
            $state = model('Config')->save($params, ['id'=>$id]);
            if ($state !== false) {
                return success_json();
            }
            return error_json();
        }
        $info = model('Config')->find($id);
        return view('', [
            'info' => $info,
        ]);
    }

    public function delete($id = "")
    {
        if ($id != "") {
            $state = model('Config')->where(['id'=>$id])->delete();
            if ($state !== false) {
                return success_json();
            }
            return error_json();
        }
    }
}
