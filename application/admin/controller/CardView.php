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
            $type = \request()->post('type');

            if(!empty($username)) {
                $map['B.username'] = ['like', "%{$username}%"];
            }
            
            if(!empty($nickname)) {
                $map['B.nickname'] = ['like', "%{$nickname}%"];
            }

            if(!empty($title)) {
                $map['C.title'] = ['like', "%{$title}%"];
            }

            if(!empty($type)) {
                $map['A.type'] = $type;
            }

            $data = $CardView->alias('A')
                    ->join($User->getTable().' B', 'A.uid=B.id', 'left')
                    ->join($Resource->getTable().' C', 'A.resources_id=C.id', 'left')
                    ->join($Card->getTable().' D', 'A.card_id=D.id', 'left')
                    ->field('A.id,A.create_time,A.type,B.username,B.nickname,C.title,D.name')
                    ->where($map)->limit($offset, $limit)->order('id desc')->select();
            $count = $CardView->alias('A')
                ->join($User->getTable().' B', 'A.uid=B.id', 'left')
                ->join($Resource->getTable().' C', 'A.resources_id=C.id', 'left')
                ->join($Card->getTable().' D', 'A.card_id=D.id', 'left')
                ->where($map)->count();

            foreach ($data as $k => $v) {
                if(!empty($title)){
                    $v['title'] = str_ireplace($title, '<font color="red">' . $title . '</font>', $v['title']);
                }

                if(!empty($username)){
                    $v['username'] = str_ireplace($username, '<font color="red">' . $username . '</font>', $v['username']);
                }

                if(!empty($nickname)){
                    $v['nickname'] = str_ireplace($nickname, '<font color="red">' . $nickname . '</font>', $v['nickname']);
                }
                $data[$k] =$v;
            }
            return json(['data' => ['count' => $count, 'list' => $data]], 200);
        }
        getAdminLog(" 查看 名片查看记录表 ");
        return view('',
        [
            'meta_title' => '名片·查看记录表',
            'type' => $CardView->type,
        ]);
    }




}
