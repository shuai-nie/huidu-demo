<?php

namespace app\admin\controller;

use think\Db;
use think\Loader;

class Config extends Base
{
    function _initialize()
    {
        parent::_initialize();
        $this->model = 'config';
    }

    public function index()
    {
//        var_dump(checkAuth('adminedit'));
//        exit();
        if (request()->isPost()) {
            $page  = $this->page;
            $limit = $this->limit;
            $limit = ($page - 1) * $limit . ",$limit";
            $map   = [];
            $list  = model('Config')->where($map)->limit($limit)->select();
            $count = model('Config')->where($map)->count();
            return json(['data' => ['count' => $count, 'list' => $list]], 200);
        }
        return view();
    }

    public function add()
    {
        if (request()->isPost()) {
            $params        = input('post.');
            $data['value'] = !empty($params['info1']) ? $params['info1'] : $params['info2'];
            if (empty($data['value'])) {
                error_callback();
                exit;
            }
            $data['key']     = $params['key'];
            $data['remarks'] = $params['remarks'];
            $state           = model('')->save($data);
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
            $params        = input('post.');
            $data['value'] = !empty($params['info1']) ? $params['info1'] : $params['info2'];
            if (empty($data['value'])) {
                error_callback();
                exit;
            }
            $data['id']      = $params['id'];
            $data['key']     = $params['key'];
            $data['remarks'] = $params['remarks'];
            $this->common->save_one($this->model, $data);
        }
        $info       = $this->common->get_find($this->model, $id);
        $info['ue'] = 0;
        if (in_array($info['key'], ['about_us', 'use_to_know', 'fuwuxieyi'])) {
            $info['ue'] = 1;
        }
        return view('', [
            'info' => $info,
        ]);
    }

    public function delete($id = "")
    {
        if ($id != "") {
            $this->common->delete($this->model, $id);
        }
    }
}