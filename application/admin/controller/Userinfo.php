<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Userinfo extends Controller
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
            $page = Request()->param('page');
            $limit = Request()->param('limit');
            $offset = ($page - 1) * $limit;
            $data = model("UserInfo")->alias('A')
                ->join(model('UserRecharge')->getTable() . ' B', 'A.user_recharge_id=B.id', 'left')
                ->join(model('UserConsume')->getTable() . ' D', 'A.user_recharge_id=D.user_recharge_id', 'left')
                ->join(model('Package')->getTable() . ' C', 'B.package_id=C.id', 'left')
                ->where($map)->field('A.*,B.start_time,B.end_time,C.title,D.used_flush,D.used_publish')
                ->limit($offset, $limit)->select();
            $count =model("UserInfo")->alias('A')
                ->join(model('UserRecharge')->getTable() . ' B', 'A.user_recharge_id=B.id', 'left')
                ->join(model('UserConsume')->getTable() . ' D', 'A.user_recharge_id=D.user_recharge_id', 'left')
                ->join(model('Package')->getTable() . ' C', 'B.package_id=C.id', 'left')
                ->where($map)->count();
            return json(['data'=>['count'=>$count, 'list'=>$data]], 200);
        }
        return view();
    }

    // 变更套餐
    public function change($id)
    {
        if(Request()->isPost()) {
            $_post = Request()->param();
            $time = time();
            model('UserRecharge')->save([
                'uid'        => $_post['uid'],
                'package_id' => $_post['package_id'],
                'start_time' => $time,
                'end_time'   => $time,
            ]);
            $recharge_id = model('UserRecharge')->getLastInsID();
            $Package = model('Package')->find($_post['package_id']);
            model('UserConsume')->save([
                'uid'              => $_post['uid'],
                'user_recharge_id' => $recharge_id,
                'used_flush'       => $Package['flush'],
                'used_publish'     => $Package['publish'],
            ]);
            $state = model('UserInfo')->save(['user_recharge_id'=>$recharge_id], ['uid'=>$_post['uid']]);
            if($state !== false) {
                return success_json(lang('PACKAGEDISTRIBUTION'). lang('Success'));
            }
            return error_json(lang('PACKAGEDISTRIBUTION'). lang('Fail'));
        }
        $userInfo = model("UserInfo")->alias('A')
            ->join(model('UserRecharge')->getTable().' B', 'A.user_recharge_id=B.id', 'left')
            ->field('A.*,B.package_id')
            ->find(['A.id'=>$id]);
        $package = model('Package')->where(['status'=>1])->select();
        return view('', ['userInfo'=>$userInfo, 'package'=>$package]);
    }

    // 延期套餐
    public function continues($id)
    {
        $userInfo = model("UserInfo")->alias('A')
            ->join(model('UserRecharge')->getTable().' B', 'A.user_recharge_id=B.id', 'left')
            ->join(model('UserConsume')->getTable().' C', 'A.user_recharge_id=C.user_recharge_id', 'left')
            ->field('A.*,B.package_id,B.start_time,B.end_time,C.used_flush,C.used_publish')
            ->find(['A.id'=>$id]);
        if(Request()->isPost()) {
            $_post = Request()->param();
            $endtime = $userInfo['end_time'] + 30*24*60*60*$_post['time'];

            model('UserRecharge')->save([
                'uid'        => $_post['uid'],
                'package_id' => $_post['package_id'],
                'start_time' => $userInfo['start_time'],
                'end_time'   => $endtime,
            ]);
            $recharge_id = model('UserRecharge')->getLastInsID();
            $Package = model('Package')->find($_post['package_id']);
            model('UserConsume')->save([
                'uid'              => $_post['uid'],
                'user_recharge_id' => $recharge_id,
                'used_flush'       => $userInfo['used_flush'],
                'used_publish'     =>$userInfo['used_publish'],
            ]);
            $state = model('UserInfo')->save(['user_recharge_id'=>$recharge_id], ['uid'=>$_post['uid']]);
            if($state !== false) {
                return success_json(lang('PackageContinue'). lang('Success'));
            }
            return error_json(lang('PackageContinue'). lang('Fail'));
        }

        $package = model('Package')->where(['status'=>1])->select();
        return view('', ['userInfo'=>$userInfo, 'package'=>$package]);
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
