<?php

namespace app\admin\controller;

class PackageLog extends Banner
{
    protected $model;
    protected $state = [1 => '増', 2 => '减'];
    protected $web = [1 => '后端', 2 => '前端'];
    public function _initialize()
    {
        parent::_initialize();
        $this->model = model('PackageLog');
    }

    /**
     * 显示资源列表
     * @return \think\Response
     */
    public function index()
    {
        if (\request()->isPost()){
            $map = [];
            $page = request()->post('page');
            $limit = request()->post('limit');
            $username = request()->post('username');
            $type = request()->post('type');
            $state = request()->post('state');
            $offset = ($page - 1) * $limit;
            if(!empty($username)){
                $map['C.username'] = ['like', "%{$username}%"];
            }
            if(!empty($type)){
                $map['A.type'] = $type;
            }
            if(!empty($state)){
                $map['A.state'] = $state;
            }

            $data = $this->model->alias('A')
                ->join(model('Resource')->getTable()." B", "A.resource_id=B.id", 'left')
                ->join(model('User')->getTable()." C", "C.id=A.uid", 'left')
                ->join(model('Package')->getTable()." D", "D.id=A.package_id", 'left')
                ->field('A.*,B.title as resource_title,D.title as package_title,C.username')
                ->where($map)->order('A.id desc')->limit($offset, $limit)->select();
            $count = $this->model->alias('A')
                ->join(model('Resource')->getTable()." B", "A.resource_id=B.id", 'left')
                ->join(model('User')->getTable()." C", "C.id=A.uid", 'left')
                ->join(model('Package')->getTable()." D", "D.id=A.package_id", 'left')
                ->where($map)->count();
            return json(['data'=>['count'=>$count, 'list'=>$data]], 200);
        }
        return view('', ['state' => $this->state]);
    }

}
