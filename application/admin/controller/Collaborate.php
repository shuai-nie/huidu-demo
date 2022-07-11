<?php
namespace app\admin\controller;

class Collaborate extends Base
{

    public function _initialize()
    {
        $this->assign('meta_title', '联系提交信息');
        parent::_initialize();
    }

    public function index()
    {
        if(request()->isPost()) {
            $Collaborate = model('Collaborate');
            $page = request()->post('page', 1);
            $limit = request()->post('limit', 10);
            $offset = ($page - 1) * $limit;

            $map = [];

            $count = $Collaborate->where($map)->count();
            $data = $Collaborate->where($map)->limit($offset, $limit)->select();

            return json(['data'=>['count'=>$count, 'list'=>$data]], 200);

        }
        return view('', []);
    }

    public function create()
    {
        $Collaborate = model('Collaborate');
        if(request()->isPost()) {
            $_post = request()->post();
            $state = $Collaborate->save($_post);
            if($state !== false) {
                return success_json('提交成功');
            }
            return error_json('提交成功');
        }
        return view('', []);
    }

    public function update()
    {
        $Collaborate = model('Collaborate');
        $id = request()->param('id');
        if(request()->isPost()) {
            $_post = request()->post();
            $state = $Collaborate->save($_post, ['id' => $id]);
            if($state !== false) {
                return success_json('提交成功');
            }
            return error_json('提交成功');
        }
        $data = $Collaborate->where(['id'=>$id])->find();
        return view('', ['data'=>$data]);
    }

    public function delete()
    {
        $Collaborate = model('Collaborate');
        $id = request()->param('id');
        $state = $Collaborate->save(['status' => 0], ['id' => $id]);
        if($state !== false) {
            return success_json('提交成功');
        }
        return error_json('提交成功');

    }

}