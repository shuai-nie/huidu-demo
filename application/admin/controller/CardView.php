<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class CardView extends Controller
{

    /**
     * 显示资源列表
     * @return \think\Response
     */
    public function index()
    {
        $CardView = model('CardView');
        $Card = model('Card');
        $User = model('User');
        $Resource = model('Resource');
        if(\request()->isPost()){
            $map = [];
            $limit = \request()->post('limit');
            $page = \request()->post('page', 1);
            $offset = ($page - 1) * $limit;
            $username = \request()->post('username');
            $nickname = \request()->post('nickname');
            $title = \request()->post('title');
            if(!empty($username)) {
                $map['B.username'] = ['like', "%{$username}%"];
            }
            if(!empty($nickname)) {
                $map['B.nickname'] = ['like', "%{$nickname}%"];
            }
            if(!empty($title)) {
                $map['C.title'] = ['like', "%{$title}%"];
            }

            $data = $CardView->alias('A')
                    ->join($User->getTable().' B', 'A.uid=B.id', 'left')
                    ->join($Resource->getTable().' C', 'A.resources_id=C.id', 'left')
                    ->join($Card->getTable().' D', 'A.card_id=D.id', 'left')
                    ->field('A.id,A.create_time,B.username,B.nickname,C.title,D.name')
                    ->where($map)->limit($offset, $limit)->order('id desc')->select();
            $count = $CardView->alias('A')
                ->join($User->getTable().' B', 'A.uid=B.id', 'left')
                ->join($Resource->getTable().' C', 'A.resources_id=C.id', 'left')
                ->join($Card->getTable().' D', 'A.card_id=D.id', 'left')
                ->where($map)->count();
            return json(['data' => ['count' => $count, 'list' => $data]], 200);
        }
        return view('',
        [
            'meta_title' => '名片·查看记录表'
        ]);
    }




}
