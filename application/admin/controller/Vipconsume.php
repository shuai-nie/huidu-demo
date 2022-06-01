<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use util\Telegram;

class Vipconsume extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        if(Request()->isPost()) {
            $map = [];
            $page = Request()->post('page');
            $limit = Request()->post('limit');
            $offset = ($page - 1) * $limit;
            $username = request()->post('username');
            $nickname = request()->post('nickname');
            if(!empty($username)) {
                $map['D.username'] = ['like', "%{$username}%"];
            }
            if(!empty($nickname)) {
                $map['D.nickname'] = ['like', "%{$nickname}%"];
            }

            $UserRecharge = model('UserRecharge');
            $User = model('User');
            $Package = model('Package');
            $data = $UserRecharge->alias('A')
                ->join($Package->getTable(). " C", "C.id=A.package_id")
                ->join($User->getTable()." D", "A.uid=D.id")
                ->field('A.id,A.uid,A.package_id,A.start_time,A.end_time,A.used_flush,A.used_publish,A.flush,A.publish,C.title,A.remarks,A.view_provide,A.view_provide_give,A.view_demand,A.used_view_demand,A.used_view_provide')
                ->where($map)->limit($offset, $limit)->order('id desc')->select();
            $count = $UserRecharge->alias('A')
                ->join($Package->getTable(). " C", "C.id=A.package_id")
                ->join($User->getTable()." D", "A.uid=D.id")
                ->where($map)->count();
            foreach ($data as $k => $v) {
                if (!empty( $v['uid'])) {
                    $CacheUser = CacheUser($v['uid']);
                    $v['username'] = $CacheUser['username'];
                    $v['nickname'] = $CacheUser['nickname'];
                    $v['head_url'] = $CacheUser['head_url'];
                }
                $data[$k] = $v;
            }
            return json(['data'=>['count'=>$count, 'list'=>$data]], 200);
        }
        return view('');
    }

}
