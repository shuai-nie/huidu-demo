<?php
namespace app\admin\controller;

class Order extends Baseti
{

    public function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
    }

    public function index()
    {
        if(request()->isPost()) {
            $page = request()->post('page', 1);
            $limit = request()->post('limit', 10);
            $offset = ($page - 1) * $limit;
            $order = model('order');
            $user = model('User');
            $resource = model('Resource');
            $packagePrice = model('PackagePrice');
            $package = model('Package');
            $map = [];
            $data = $order->alias('A')
                ->join($user->getTable().' B', 'A.uid=B.id', 'left')
                ->join($resource->getTable().' C', 'A.rid=C.id', 'left')
                ->join($package->getTable().' D', 'A.old_package_id=D.id', 'left')
                ->join($package->getTable().' E', 'A.new_package_id=E.id', 'left')
                ->join($packagePrice->getTable().' F', 'A.package_price_id=F.id', 'left')
                ->field('A.*,B.username,B.nickname,C.title as resource_title,D.title as old_package_title,E.title as new_package_title,F.type as price_type,F.old_amount as price_old_amount,F.new_amount as price_new_amount')
                ->where($map)->order('A.status asc, A.id desc')->limit($offset, $limit)->select();
            foreach ($data as $k => $v) {
                $v['key'] = $k+ ($page-1)*$limit+1;
                $data[$k] = $v;
            }
            $count = $order->alias('A')
                ->where($map)->count();
            return json(['data' => [ 'count' => $count, 'list' => $data]], 200);
        }
        return view('', [
            'meta_title' => '订单管理',
        ]);
    }

    public function examine()
    {
        $order = model('order');
        $user = model('User');
        $resource = model('Resource');
        $packagePrice = model('PackagePrice');
        $package = model('Package');
        $id = request()->param('id');
        $info = $order->find($id);

        if(request()->isPost()) {
            $_post = request()->post();

            if($_post['status'] == 0){
                // 未审核
                return $this->success('数据未审核');
            }elseif ($_post['status'] == 1){
                // 审核通过
                if($info['type'] == 0) {
                    // vip 订单
                    $state = $this->userPackage($info['uid'], $info['new_package_id'], $info['buy_type'], $info);
                }elseif ($info['type'] == 1) {
                    // 置顶
                    $state = $this->resourceTop($info);
                }
                if($state !== false){
                    return $this->success('数据通过');
                }
                return $this->error('数据审核失败');

            }elseif ($_post['status'] == 2){
                // 审核不通过
                $state = $order->isUpdate(true)->save([
                    'status' => $_post['status'],
                    'feedback' => $_post['feedback'],
                ], ['id' => $id]);
                if($state !== false) {
                    return $this->success('数据不通过');
                } else {
                    return $this->error('数据修改失败');
                }
            }
        }

        $map = ['A.id' => $id];
        $orderInfo = $order->alias('A')
            ->join($user->getTable().' B', 'A.uid=B.id', 'left')
            ->join($resource->getTable().' C', 'A.rid=C.id', 'left')
            ->join($package->getTable().' D', 'A.old_package_id=D.id', 'left')
            ->join($package->getTable().' E', 'A.new_package_id=E.id', 'left')
            ->join($packagePrice->getTable().' F', 'A.package_price_id=F.id', 'left')
            ->field('A.*,B.username,B.nickname,C.title as resource_title,D.title as old_package_title,E.title as new_package_title,F.type as price_type,F.old_amount as price_old_amount,F.new_amount as price_new_amount')
            ->where($map)->find();
        return view('', [
            'info' => $orderInfo,
        ]);
    }

    // 资源置顶
    private function resourceTop($info)
    {
        $packagePrice = model('PackagePrice');
        $resource = model('Resource');
        $packagePriceInfo = $packagePrice->find($info['package_price_id']);
        $time = time();
        $endTime = $time;
        switch ($packagePriceInfo['type']){
            case 1:
                $endTime =+ 86400*30;
                break;
            case 2:
                $endTime =+ 86400*90;
                break;
            case 3:
                $endTime =+ 86400*365;
                break;
        }

        $state = $resource->isUpdate(true)->allowField(true)->save([
            'top_start_time' => $time,
            'top_end_time' => $endTime,
        ], ['id' => $info['rid']]);
        if($state !== false){
            return true;
        }
        return false;
    }

    public function userPackage($uid, $package_id, $type, $order)
    {
        $package = model('Package');
        $userRecharge = model('userRecharge');
        $userInfo = model('userInfo');
        $packagePrice = model('PackagePrice');
        $userInfoData = $userInfo->where(['uid'=>$uid])->find();
        $PackageData = $package->where(['id'=>$package_id])->find();
        $userRechargeInfo = $userRecharge->where(['id' => $userInfoData['user_recharge_id']])->find();
        $packagePriceInfo = $packagePrice->find($order['package_price_id']);
        /**
         * 购买/升级
         * 购买套餐结束时间小于结束时间，则升级，添加到期后返回降级套餐id
         * 购买套餐结束时间大于结束时间，则直接升级，不添加到期返回降级套餐id
         *
         * 续费
         * 直接添加结束时间往后延续
         */

        $time = time();
        $start_time = $time;
        $endTime = 0;
        switch ($packagePriceInfo['type']){
            case 1:
                $endTime =+ 86400*30;
                break;
            case 2:
                $endTime =+ 86400*90;
                break;
            case 3:
                $endTime =+ 86400*365;
                break;
        }
        $end_time = $time + $endTime;
        $recharge_id = 0;
        if($type == 0){
            // 0 购买

            if (($time + $endTime) < $userRechargeInfo['end_time'] && $userRechargeInfo['package_id'] != 1) {
                $t = $userRechargeInfo['end_time'] - $time;
                $userRecharge->allowField(true)->isUpdate(false)->save([
                    'uid'               => $uid,
                    'package_id'        => $package_id,
                    'pay_price'         => $packagePriceInfo['old_amount'],
                    'flush'             => $PackageData['flush'],
                    'publish'           => $PackageData['publish'],
                    'view_demand'       => $PackageData['view_demand'],
                    'view_provide'      => $PackageData['view_provide'],
                    'view_provide_give' => $PackageData['view_provide_give'],
                    'used_flush'        => $userRechargeInfo['used_flush'],
                    'used_publish'      => $userRechargeInfo['used_publish'],
                    'used_view_demand'  => $userRechargeInfo['used_view_demand'],
                    'used_view_provide' => $userRechargeInfo['used_view_provide'],
                    'start_time'        =>$end_time, // 开始时间
                    'end_time' => $end_time + $t, // 结束时间
                    'remarks'           => '审核-购买套餐 ',
                ]);
                $recharge_id = $userRecharge->id;
            }
            $userRecharge->allowField(true)->isUpdate(false)->save([
                'uid' => $uid,
                'package_id' => $package_id,
                'pay_price' => $packagePriceInfo['old_amount'],
                'allot_recharge_id' => $recharge_id,
                'flush' => $PackageData['flush'],
                'publish' => $PackageData['publish'],
                'view_demand' => $PackageData['view_demand'],
                'view_provide' => $PackageData['view_provide'],
                'view_provide_give' => $PackageData['view_provide_give'],
                'used_flush' => 0,
                'used_publish' => $userRechargeInfo['used_publish'],
                'used_view_demand' => 0,
                'used_view_provide' => 0,
                'start_time' => time(), // 开始时间
                'end_time' => $end_time, // 结束时间
                'remarks' => '审核-购买套餐 ',
            ]);
            $userRecharge_id = $userRecharge->id;
            return $userInfo->allowField(true)->isUpdate(true)->save(['user_recharge_id'=>$userRecharge_id], ['uid'=>$uid]);
        }elseif ($type== 1) {
            // 续费
            $userRecharge->allowField(true)->isUpdate(false)->save([
                'uid' => $uid,
                'package_id' => $package_id,
                'pay_price' => $packagePriceInfo['old_amount'],
                'flush' => $PackageData['flush'],
                'publish' => $PackageData['publish'],
                'view_demand' => $PackageData['view_demand'],
                'view_provide' => $PackageData['view_provide'],
                'view_provide_give' => $PackageData['view_provide_give'],
                'used_flush' => $userRechargeInfo['used_flush'],
                'used_publish' => $userRechargeInfo['used_publish'],
                'used_view_demand' => $userRechargeInfo['used_view_demand'],
                'used_view_provide' => $userRechargeInfo['used_view_provide'],
                'start_time' => time(), // 开始时间
                'end_time' => $userRechargeInfo['end_time'] + $endTime, // 结束时间
                'remarks' => '审核-续费套餐 ',
            ]);
            $userRecharge_id = $userRecharge->id;
            return $userInfo->allowField(true)->isUpdate(true)->save(['user_recharge_id'=>$userRecharge_id], ['uid'=>$uid]);
        }elseif ($type == 2){
            if (($time + $endTime) < $userRechargeInfo['end_time'] && $userRechargeInfo['package_id'] != 1) {
                $t = $userRechargeInfo['end_time'] - $time;
                $userRecharge->allowField(true)->isUpdate(false)->save([
                    'uid'               => $uid,
                    'package_id'        => $package_id,
                    'pay_price'         => $packagePriceInfo['old_amount'],
                    'flush'             => $PackageData['flush'],
                    'publish'           => $PackageData['publish'],
                    'view_demand'       => $PackageData['view_demand'],
                    'view_provide'      => $PackageData['view_provide'],
                    'view_provide_give' => $PackageData['view_provide_give'],
                    'used_flush'        => $userRechargeInfo['used_flush'],
                    'used_publish'      => $userRechargeInfo['used_publish'],
                    'used_view_demand'  => $userRechargeInfo['used_view_demand'],
                    'used_view_provide' => $userRechargeInfo['used_view_provide'],
                    'start_time'        =>$end_time, // 开始时间
                    'end_time' => $end_time + $t, // 结束时间
                    'remarks'           => '审核-购买套餐 ',
                ]);
                $recharge_id = $userRecharge->id;
            }
            // 2 升级
            $userRecharge->allowField(true)->isUpdate(false)->save([
                'uid' => $uid,
                'package_id' => $package_id,
                'pay_price' => $packagePriceInfo['old_amount'],
                'allot_recharge_id' => $recharge_id,
                'flush' => $PackageData['flush'],
                'publish' => $PackageData['publish'],
                'view_demand' => $PackageData['view_demand'],
                'view_provide' => $PackageData['view_provide'],
                'view_provide_give' => $PackageData['view_provide_give'],
                'used_flush' => 0,
                'used_publish' => $userRechargeInfo['used_publish'],
                'used_view_demand' => 0,
                'used_view_provide' => 0,
                'start_time' => time(), // 开始时间
                'end_time' => $end_time, // 结束时间
                'remarks' => '审核-升级套餐 ',
            ]);
            $userRecharge_id = $userRecharge->id;
            return $userInfo->allowField(true)->isUpdate(true)->save(['user_recharge_id'=>$userRecharge_id], ['uid'=>$uid]);
        }else {
            return false;
        }

    }

}