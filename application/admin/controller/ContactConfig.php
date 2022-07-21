<?php
namespace app\admin\controller;

class ContactConfig extends Base
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        if (request()->isPost()) {
            $page  = request()->param('page');
            $limit = request()->param('limit');
            $offset = ($page - 1) * $limit ;
            $map   = ['type'=>1];
            $list  = model('Config')->where($map)->limit($offset, $limit)->order('id desc')->select();
            $count = model('Config')->where($map)->count();
            return json(['data' => ['count' => $count, 'list' => $list]], 200);
        }
        return view('', [
            'meta_title' => '联系方式配置',
        ]);

    }

}