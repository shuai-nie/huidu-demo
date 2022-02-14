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
                ->join(model('UserConsume')->getTable() . ' D', 'A.user_recharge_id=D.user_recharge_id', 'left')
                ->join(model('Package')->getTable() . ' C', 'B.package_id=C.id', 'left')
                ->join(model('User')->getTable(). ' E', 'A.uid=E.id')
                ->where($map)->field('A.*,B.start_time,B.end_time,C.title,D.used_flush,D.used_publish')
                ->limit($offset, $limit)->select();
            $count = model("UserInfo")->alias('A')
                ->join(model('UserRecharge')->getTable() . ' B', 'A.user_recharge_id=B.id', 'left')
                ->join(model('UserConsume')->getTable() . ' D', 'A.user_recharge_id=D.user_recharge_id', 'left')
                ->join(model('Package')->getTable() . ' C', 'B.package_id=C.id')
                ->join(model('User')->getTable(). ' E', 'A.uid=E.id', 'left')
                ->where($map)->count();

            foreach ($data as $k=> $v) {
                if(!empty($v['uid'])) {
                    $userInfo = CacheUser($v['uid']);
                    $v['username'] = $userInfo['username'];
                    $v['nickname'] = $userInfo['nickname'];
                }
                $data[$k] = $v;
            }

            return json(['data' => ['count' => $count, 'list' => $data]], 200);
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
                    'uid' => $uid,
                    'package_id' => 1,
                    'start_time' => time(),
                    'end_time' => time(),
                ]);
                $UserRechargeId = $UserRechargeModel->id;
                model('UserConsume')->save([
                    'uid' => $uid,
                    'user_recharge_id' => $UserRechargeId,
                    'used_flush' => $packageInfo['flush'],
                    'used_publish' => $packageInfo['publish'],
                    'create_time' => time(),
                    'update_time' => time(),
                ]);

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
            Cache::rm("User_" . $UserInfo->uid);
            if($state !== false){
                return success_json();
            }
            return error_json();
        }
        return view('', [
            'UserInfo' => $UserArr
        ]);
    }

}
