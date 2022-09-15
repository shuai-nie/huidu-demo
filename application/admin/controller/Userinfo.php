<?php
namespace app\admin\controller;

use think\Config;
use think\Controller;
use think\Db;
use think\Exception;
use think\Log;
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
        if(\request()->isPost()) {
            $map = ['E.status'=>1];
            $page = \request()->post('page');
            $limit = \request()->post('limit', Config::get('paginate')['list_rows']);
            $username = \request()->post('username');
            $nickname = \request()->post('nickname');
            $package_id = \request()->post('package_id');
            $field = \request()->post('field');
            $type = \request()->post('type');
            $isweb = \request()->post('isweb');
            $is_tg = \request()->post('is_tg', 0);
            if(!empty($field) && !empty($type) ) {
                $order = "A." . $field . ' ' . $type;
            }else {
                $order = "E.id desc";
            }

            if(!empty($username)) {
                $map['E.username'] = ['like', "%{$username}%"];
            }
            if(!empty($nickname)) {
                $map['E.nickname'] = ['like', "%{$nickname}%"];
            }
            if(!empty($package_id)) {
                $map['B.package_id'] = $package_id;
            }
            if(!empty($isweb)) {
                $map['E.isweb'] = $isweb;
            }
            if($is_tg != 0){
                $map['E.chat_id'] = ['neq', ''];
            }
            $offset = ($page - 1) * $limit;
            $data = model("UserInfo")->alias('A')
                ->join(model('UserRecharge')->getTable() . ' B', 'A.user_recharge_id=B.id', 'left')
                ->join(model('Package')->getTable() . ' C', 'B.package_id=C.id', 'left')
                ->join(model('User')->getTable(). ' E', 'A.uid=E.id', 'left')
                ->join([model('Channel')->getTable()=> 'F'], 'E.channel_id=F.id', 'left')
                ->where($map)->field('A.*,B.start_time,B.end_time,C.title,B.used_flush,B.used_publish,B.flush,B.publish,B.view_provide,B.view_provide_give,B.view_demand,B.used_view_demand,B.used_view_provide,E.username,E.nickname,E.head_url,E.mobile,E.email,E.telegram,E.chat_id,E.isweb,F.channel_name')
                ->limit($offset, $limit)->order($order)->select();
            $count = model("UserInfo")->alias('A')
                ->join(model('UserRecharge')->getTable() . ' B', 'A.user_recharge_id=B.id', 'left')
                ->join(model('Package')->getTable() . ' C', 'B.package_id=C.id', 'left')
                ->join(model('User')->getTable(). ' E', 'A.uid=E.id', 'left')
                ->join([model('Channel')->getTable()=> 'F'], 'E.channel_id=F.id', 'left')
                ->where($map)->count();

            return json(['data' => ['count' => $count, 'list' => $data]], 200);
        }
        $id = \request()->get('id', 0);
        $packageAll = model('Package')->where(['status'=>1])->field('id,title')->select();
        return view('', [
            'id' => $id,
            'packageAll' => $packageAll,
            'meta_title' => '用户列表'
        ]);
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
            ->field('A.*,B.package_id,C.username,C.nickname,B.used_flush,B.used_publish,B.view_demand,B.view_provide,B.view_provide_give,B.used_view_demand,B.used_view_provide')
            ->find(['A.id'=>$id]);
        if(Request()->isPost()) {
            $_post = Request()->param();

            $Package = model('Package')->find($_post['package_id']);
            $time = time();
            $save = [
                'uid'                => $_post['uid'],
                'package_id'         => $_post['package_id'],
                'start_time'         => $time,
                'flush'              => $Package['flush'],
                'publish'            => $Package['publish'],
                'view_demand'        => $Package['view_demand'],
                'view_provide'       => $Package['view_provide'],
                'view_provide_give'  => $Package['view_provide_give'],
                'used_publish'       => $userInfo['used_publish'],
                'view_contacts'      => $userInfo['view_contacts'],
                'used_flush'         => 0,
                'used_view_demand'   => 0,
                'used_view_provide'  => 0,
                'used_view_contacts' => 0,
                'remarks'            => '变更套餐',
            ];

            if($_post['time'] > 0){
                $save['end_time'] = $time + $_post['time'] * 30*60*60*24;
            } else {
                $save['end_time'] = $time + 30*60*60*24;

            }

            $UserRecharge->data($save)->save();
            $recharge_id = $UserRecharge->id;

            $state = model('UserInfo')->isUpdate(true)->save(['user_recharge_id'=>$recharge_id], ['uid'=>$_post['uid']]);
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
            ->field('A.*,B.package_id,B.start_time,B.end_time,B.used_flush,B.used_publish,B.flush,B.publish,D.username,D.nickname,B.view_demand,B.view_provide,B.view_provide_give,B.view_contacts,B.used_view_demand,B.used_view_provide,B.used_view_contacts')
            ->find(['A.id'=>$id]);
        if(Request()->isPost()) {
            $_post = Request()->param();
            $endtime = $userInfo['end_time'] + 30*24*60*60*$_post['time'];
            $UserRecharge->save([
                'uid'          => $_post['uid'],
                'package_id'   => $_post['package_id'],
                'start_time'        => $userInfo['start_time'],
                'end_time'          => $endtime,
                'flush'             => $userInfo['flush'],
                'publish'           => $userInfo['publish'],
                'view_demand'       => $userInfo['view_demand'],
                'view_provide_give' => $userInfo['view_provide_give'],
                'view_provide'      => $userInfo['view_provide'],
                'view_contact'      => $userInfo['view_contact'],
                'used_flush'        => $userInfo['used_flush'],
                'used_publish'      => $userInfo['used_publish'],
                'used_view_demand'  => $userInfo['used_view_demand'],
                'used_view_provide' => $userInfo['used_view_provide'],
                'used_view_contacts' => $userInfo['used_view_contacts'],
                'remarks'           => '延期套餐',
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
            $data['business_type'] = isset($data['business_type']) ? implode('|', $data['business_type']) : '';
            $data['industry'] = isset($data['industry']) ? $data['industry'] != '|' ? implode('|', $data['industry']) : $data['industry'] : '';
            $data['region'] = isset($data['region']) ? $data['region'] != '|' ?  implode('|', $data['region']) : $data['region'] : '';

            $number = GetRandStr(16);
            $data['pwd'] = md5(md5($data['pwd']). $number);
            $data['salt'] = $number;
            $userModel = model('User');
            $count = $userModel->where(['mobile'=>$data['mobile'],'status'=>1])->count();
            if($count > 0) {
                return error_json('手机号已存在,请修改');
            }
            $count = $userModel->where(['email'=>$data['email'],'status'=>1])->count();
            if($count > 0) {
                return error_json('email已存在,请修改');
            }

            Db::startTrans();
            try {
                $userModel->save($data);
                $uid = $userModel->id;
                $packageInfo = model('Package')->where(['id'=>1])->find();
                $UserRechargeModel = model('UserRecharge');
                $UserRechargeModel->data([
                    'uid'               => $uid,
                    'package_id'        => 1,
                    'start_time'        => time(),
                    'flush'             => $packageInfo['flush'],
                    'publish'           => $packageInfo['publish'],
                    'view_provide'      => $packageInfo['view_provide'],
                    'view_provide_give' => $packageInfo['view_provide_give'],
                    'view_demand'       => $packageInfo['view_demand'],
                    'view_contacts'     => $packageInfo['view_contacts'],
                    'end_time'          => time() + 30 * 60 * 60 * 24,
                    'remarks'           => '注册账户' . $uid,
                ])->save();
                $UserRechargeId = $UserRechargeModel->id;
                model('UserInfo')->data([
                    'uid' => $uid,
                    'user_recharge_id' => $UserRechargeId
                ])->save();
                model('UserDemand')->data([
                    'uid' => $uid,
                    'business_type' => $data['business_type'],
                    'industry' => $data['industry'],
                    'region' => $data['region'],
                ])->save();
                $state = true;
                Db::commit();
            }catch (Exception $e){
                $state = false;
                Db::rollback();
            }
            if($state !== false){
                return success_json(lang('CreateSuccess', [ lang('User')]));
            }
            return error_json(lang('CreateFail', [ lang('User')]));
        }
        $RESOURCES_TYPE = model('DataDic')->selectType(['data_type_no'=>'RESOURCES_TYPE', 'status'=>1]);
        $RESOURCES_REGION = model('DataDic')->selectType(['data_type_no'=>'RESOURCES_REGION', 'status'=>1]);
        $ADVERT_ATTRIBUTE = model('DataDic')->selectType(['data_type_no'=>'ADVERT_ATTRIBUTE', 'status'=>1]);
        $FIRM_SCALE = model('DataDic')->selectType(['data_type_no'=>'FIRM_SCALE', 'status'=>1]);
        return view('', [
            'RESOURCES_TYPE' => $RESOURCES_TYPE,
            'RESOURCES_REGION' => $RESOURCES_REGION,
            'ADVERT_ATTRIBUTE' => $ADVERT_ATTRIBUTE,
            'FIRM_SCALE' => $FIRM_SCALE,
        ]);
    }

    public function edit()
    {
        $id = request()->param('id');
        $UserModel = model("User");
        $UserDemand = model('UserDemand');
        $UserInfo = model('UserInfo')->where(['id' => $id])->find();
        $UserArr = $UserModel->where(['id' => $UserInfo->uid])->find();
        if(request()->isPost()) {
            $data = request()->post();
            if(!empty($data['pwd'])) {
                $data['pwd'] = md5(md5($data['pwd']). $UserArr->salt);
            } else {
                unset($data['pwd']);
            }

            $data['business_type'] = isset($data['business_type']) ? implode('|', $data['business_type']) : '';
            $data['industry'] = isset($data['industry']) ? $data['industry'] != '|' ? implode('|', $data['industry']) : $data['industry'] : '';
            $data['region'] = isset($data['region']) ? $data['region'] != '|' ?  implode('|', $data['region']) : $data['region'] : '';

            Db::startTrans();
            try {
                $UserModel->allowField(true)->isUpdate(true)->save($data, ['id' => $UserInfo->uid]);
                $count = $UserDemand->where(['uid' => $UserInfo->uid])->count();
                if($count == 0){
                    $UserDemand->allowField(true)->isUpdate(false)->data([
                        'uid'           => $UserInfo->uid,
                        'business_type' => $data['business_type'],
                        'industry'      => $data['industry'],
                        'region'        => $data['region'],
                    ])->save();
                }else {
                    $UserDemand->allowField(true)->isUpdate(true)->save([
                        'business_type' => $data['business_type'],
                        'industry'      => $data['industry'],
                        'region'        => $data['region'],
                    ], ['uid' => $UserInfo->uid]);
                }

                $state = true;
                Db::commit();
            }catch (Exception $e) {
                $state = false;
                Db::rollback();
            }

            if ($state !== false) {
                return success_json(lang('EditSuccess', [ lang('User')]));
            }
            return error_json(lang('EditFail', [ lang('User')]));
        }
        $Demand = $UserDemand->where(['uid'=>$UserInfo->uid])->find();
        $RESOURCES_TYPE = model('DataDic')->selectType(['data_type_no' => 'RESOURCES_TYPE', 'status' => 1]);
        $RESOURCES_REGION = model('DataDic')->selectType(['data_type_no' => 'RESOURCES_REGION', 'status' => 1]);
        $ADVERT_ATTRIBUTE = model('DataDic')->selectType(['data_type_no' => 'ADVERT_ATTRIBUTE', 'status' => 1]);
        $FIRM_SCALE = model('DataDic')->selectType(['data_type_no' => 'FIRM_SCALE', 'status' => 1]);
        if($Demand){
            $Demand['business_type'] = explode('|', $Demand['business_type']);
            if($Demand['industry'] != '|'){
                $Demand['industry'] = explode('|', $Demand['industry']);
            }
            if($Demand['region'] != '|') {
                $Demand['region'] = explode('|', $Demand['region']);
            }
        } else {
            $Demand['business_type'] = [];
            $Demand['industry'] = [];
            $Demand['region'] = [];
        }


        return view('', [
            'UserInfo'         => $UserArr,
            'RESOURCES_TYPE'   => $RESOURCES_TYPE,
            'RESOURCES_REGION' => $RESOURCES_REGION,
            'ADVERT_ATTRIBUTE' => $ADVERT_ATTRIBUTE,
            'FIRM_SCALE'       => $FIRM_SCALE,
            'Demand'           => $Demand,
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

    public function delete($id)
    {
        if($id > 0 ){
            $state = model('User')->save(['status' => 0], ['id' => $id]);
            if($state !== false) {
                return success_json(lang('DeleteSuccess', [lang('User')] ));
            }
            return error_json(lang('DeleteFail', [lang('User')]) );
        }
    }

    public function reset()
    {
        if(\request()->isPost()){
            $user = model('user');
            $uid = \request()->post('uid');
            $info = $user->where(['id'=>$uid])->find();
            $state = $user->allowField(true)->isUpdate(true)->save(['pwd' => md5(md5($info['username']) . $info['salt'])], ['id' => $uid]);
            if($state !== false) {
                return success_json('密码重置成功');
            }
            return error_json('密码重置失败');
        }

    }

}
