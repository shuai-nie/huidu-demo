<?php
namespace app\admin\controller;

class Feedback extends Base
{
    public function _initialize()
    {
        $this->assign('meta_title', 'h5·意见反馈');
        parent::_initialize();
    }

    public function index()
    {

        if(request()->isPost()){
            $user = model('User');
            $feedback = model('feedback');
            $page = request()->post('page', 1);
            $limit = request()->post('limit', 10);
            $offset = ($page - 1) * $limit;
            $map = [];
            $username = request()->post('username');
            if(!empty($username)){
                $map['B.username|B.nickname'] = ['like', '%'.$username.'%'];
            }
            $count = $feedback->alias('A')->join($user->getTable().' B', 'A.uid=B.id', 'left')->where($map)->count();
            $data = $feedback->alias('A')->join($user->getTable().' B', 'A.uid=B.id', 'left')
                ->where($map)->limit($offset, $limit)
                ->field('A.*,B.username,B.nickname')
                ->order('A.id desc')->select();
            return json(['data' => ['count' => $count, 'list' => $data]], 200);
        }
        return view('', []);
    }

}