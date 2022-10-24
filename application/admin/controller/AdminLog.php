<?php
namespace app\admin\controller;

class AdminLog extends Base
{
    public function index()
    {
        if(request()->isPost()) {
            $adminLog = model('AdminLog');
            $admin = model('admin');
            $limit = request()->post('limit');
            $page = request()->post('page', 1);
            $offset = ($page - 1) * $limit;
            $name = request()->post('name');
            $map = [];

            if(!empty($name)) {
                $map['A.text'] = ['like', '%'.$name.'%'];
            }
            $count = $adminLog->alias('A')
                ->join($admin->getTable()." B", "A.uid=B.id", "left")
                ->where($map)->count();

            $list = $adminLog->alias('A')
                ->join($admin->getTable()." B", "A.uid=B.id", "left")
                ->field('A.*,B.name as user_name')
                ->where($map)->order('id desc')->limit($offset, $limit)->select();
            $data = [
                'code' => 0, 'msg'  => '', 'data' => [
                    'count' => $count,
                    'list'  => $list,
                ],
            ];
            return json($data);
        }
        return view('', [
            'meta_title' => '日志管理'
        ]);
    }

}