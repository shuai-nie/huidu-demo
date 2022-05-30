<?php
namespace app\admin\controller;

class Order extends Base
{

    public function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
    }

    public function index()
    {
        if(request()->isPost()) {
            $page = request()->post('page', 1);
            $limit = request()->post('limit', 10);
            $offset = ($page - 1) * $limit;
            $order = model('order');
            $map = [];
            $data = $order->alias('A')->where($map)->order('id desc')->limit($offset, $limit)->select();
            foreach ($data as $k => $v) {
                $v['key'] = $k+ ($page-1)*$limit+1;
                $data[$k] = $v;
            }
            $count = $order->alias('A')
                ->where($map)->count();
            return json(['data' => [ 'count' => $count, 'list' => $data]], 200);
        }
        return view('', []);
    }

}