<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Userinfo extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        if(Request()->isPost()) {
            $map = ['E.status'=>1];
            $page = Request()->param('page');
            $limit = Request()->param('limit');
            $username = Request()->param('username');
            if(!empty($username)) {
                $map['E.username'] = ['like', "%{$username}%"];
            }
            $offset = ($page - 1) * $limit;
            $data = model("UserInfo")->alias('A')
                ->join(model('UserRecharge')->getTable() . ' B', 'A.user_recharge_id=B.id', 'left')
                ->join(model('Package')->getTable() . ' C', 'B.package_id=C.id', 'left')
                ->join(model('User')->getTable(). ' E', 'A.uid=E.id')
                ->where($map)->field('A.*,B.start_time,B.end_time,C.title,B.used_flush,B.used_publish,B.flush,B.publish,E.username,E.nickname,E.head_url,E.mobile,E.email')
                ->limit($offset, $limit)->order('E.id desc')->select();
            $count = model("UserInfo")->alias('A')
                ->join(model('UserRecharge')->getTable() . ' B', 'A.user_recharge_id=B.id', 'left')
                ->join(model('Package')->getTable() . ' C', 'B.package_id=C.id')
                ->join(model('User')->getTable(). ' E', 'A.uid=E.id', 'left')
                ->where($map)->count();

            return json(['data' => ['count' => $count, 'list' => $data]], 200);
        }
        return view();
    }

    // 变更套餐
    public function change($id)
    {
        $UserRecharge = model('UserRecharge');
        $UserInfo = model('UserInfo');
        $User = model('User');
        $userInfo = model("UserInfo")->alias('A')
            ->join(model('UserRecharge')->getTable().' B', 'A.user_recharge_id=B.id', 'left')
            ->join(model('User')->getTable()." C", 'C.id=A.uid')
            ->field('A.*,B.package_id,C.username,C.nickname,B.used_flush,B.used_publish')
            ->find(['A.id'=>$id]);
        if(Request()->isPost()) {
            $_post = Request()->param();
            $Package = model('Package')->find($_post['package_id']);
            $time = time();
            $save = [
                'uid'          => $_post['uid'],
                'package_id'   => $_post['package_id'],
                'start_time'   => $time,
                'flush'        => $Package['flush'],
                'publish'      => $Package['publish'],
                'used_flush'   => $userInfo['used_flush'],
                'used_publish' => $userInfo['used_publish'],
                'remarks'      => '变更套餐',
            ];
            if($_post['package_id'] == 1){
                $save['end_time'] = $time + 30*60*60*24;
            } else {
                if($_post['time'] > 0){
                    $save['end_time'] = $time + 30*60*60*24;
                } else {
                    $save['end_time'] = $time + $_post['time'] * 30*60*60*24;
                }
            }

            $UserRecharge->save($save);
            $recharge_id = $UserRecharge->id;


            $state = model('UserInfo')->save(['user_recharge_id'=>$recharge_id], ['uid'=>$_post['uid']]);
            if($state !== false) {
                return success_json(lang('PACKAGEDISTRIBUTION'). lang('Success'));
            }
            return error_json(lang('PACKAGEDISTRIBUTION'). lang('Fail'));
        }
        $package = model('Package')->where(['status'=>1])->select();
        return view('', ['userInfo'=>$userInfo, 'package'=>$package]);
    }

    // 延期套餐
    public function continues($id)
    {
        $UserRecharge = model('UserRecharge');
        $UserInfo = model('UserInfo');
        $User = model('User');
        $userInfo = $UserInfo->alias('A')
            ->join($UserRecharge->getTable().' B', 'A.user_recharge_id=B.id', 'left')
            ->join($User->getTable()." D", 'D.id=A.uid')
            ->field('A.*,B.package_id,B.start_time,B.end_time,B.used_flush,B.used_publish,B.flush,B.publish,D.username,D.nickname')
            ->find(['A.id'=>$id]);
        if(Request()->isPost()) {
            $_post = Request()->param();
            $endtime = $userInfo['end_time'] + 30*24*60*60*$_post['time'];
            $UserRecharge->save([
                'uid'          => $_post['uid'],
                'package_id'   => $_post['package_id'],
                'start_time'   => $userInfo['start_time'],
                'end_time'     => $endtime,
                'flush'        => $userInfo['flush'],
                'publish'      => $userInfo['publish'],
                'used_flush'   => $userInfo['used_flush'],
                'used_publish' => $userInfo['used_publish'],
                'remarks'      => '延期套餐',
            ]);
            $recharge_id = $UserRecharge->id;
            $state = model('UserInfo')->save(['user_recharge_id'=>$recharge_id], ['uid'=>$_post['uid']]);
            if($state !== false) {
                return success_json(lang('PackageContinue'). lang('Success'));
            }
            return error_json(lang('PackageContinue'). lang('Fail'));
        }

        $package = model('Package')->where(['status'=>1])->select();
        return view('', ['userInfo'=>$userInfo, 'package'=>$package]);
    }

    public function create()
    {
        if(request()->isPost()) {
            $data = request()->post();
            $number = GetRandStr(16);
            $data['pwd'] = md5(md5($data['pwd']). $number);
            $data['salt'] = $number;
            $userModel = model('User');
            $state = $userModel->save($data);
            $uid = $userModel->id;
            if($state !== false){
                $packageInfo = model('Package')->find(1);
                $UserRechargeModel = model('UserRecharge');
                $UserRechargeModel->save([
                    'uid'        => $uid,
                    'package_id' => 1,
                    'start_time' => time(),
                    'flush'      => $packageInfo['flush'],
                    'publish'    => $packageInfo['publish'],
                    'end_time'   => time() + 30 * 60 * 60 * 24,
                ]);
                $UserRechargeId = $UserRechargeModel->id;
                model('UserInfo')->save([
                    'uid' => $uid,
                    'user_recharge_id' => $UserRechargeId
                ]);
                return success_json();
            }
            return error_json();
        }
        return view();
    }
    //

    public function edit($id)
    {
        $UserModel = model("User");
        $UserInfo = model('UserInfo')->find($id);
        $UserArr = $UserModel->find($UserInfo->uid);
        if(request()->isPost()) {
            $data = request()->post();
            if(!empty($data['pwd'])) {
                $data['pwd'] = md5(md5($data['pwd']). $UserArr->salt);
            } else {
                unset($data['pwd']);
            }
            $state = $UserModel->allowField(true)->save($data, ['id'=>$UserInfo->uid]);
            if($state !== false){
                return success_json();
            }
            return error_json();
        }
        return view('', [
            'UserInfo' => $UserArr
        ]);
    }

    public function username()
    {
        if($this->request->isPost()) {
            $data = $this->request->post();
            $userInfo = model('User')->where(['username'=>$data['username']])->find();
            if($userInfo){
                return error_json('账号存在，请修改');
            }else{
                return success_json();
            }
        }
    }

}
