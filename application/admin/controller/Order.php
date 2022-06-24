<?php
namespace app\admin\controller;

class Order extends Base
{

    public function _initialize()
    {
        parent::_initialize(); // TODO: Change the autogenerated stub
    }

    public function index()
    {
        $order = model('order');
        $user = model('User');
        $resource = model('Resource');
        $packagePrice = model('PackagePriceHistory');
        $package = model('PackageHistory');

        if(request()->isPost()) {
            $page = request()->post('page', 1);
            $limit = request()->post('limit', 10);
            $username = request()->post('username');
            $type = request()->post('type');
            $new_package_id = request()->post('new_package_id');
            $status = request()->post('status');
            $map = [];
            if(!empty($username)) {
                $map['B.username|B.nickname'] = ['like', "%{$username}%"];
            }
            if(is_numeric($type)) {
                $map['A.type'] = $type;
            }
            if(is_numeric($new_package_id)) {
                $map['A.new_package_id'] = $new_package_id;
            }
            if(is_numeric($status)) {
                $map['A.status'] = $status;
            }

            $offset = ($page - 1) * $limit;

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
                if(!empty($username)) {
                    $v['username'] = str_replace($username, "<font color='red'>".$username."</font>", $v['username']);
                    $v['nickname'] = str_replace($username, "<font color='red'>".$username."</font>", $v['nickname']);
                }
                $data[$k] = $v;
            }
            $count = $order->alias('A')
                ->join($user->getTable().' B', 'A.uid=B.id', 'left')
                ->join($resource->getTable().' C', 'A.rid=C.id', 'left')
                ->join($package->getTable().' D', 'A.old_package_id=D.id', 'left')
                ->join($package->getTable().' E', 'A.new_package_id=E.id', 'left')
                ->join($packagePrice->getTable().' F', 'A.package_price_id=F.id', 'left')
                ->where($map)->count();
            return json(['data' => [ 'count' => $count, 'list' => $data]], 200);
        }

        $packageAll = model('Package')->where(['status'=>1])->field('id,title')->select();
        return view('', [
            'meta_title' => '订单管理',
            'type' => $order->type,
            'status' => $order->status,
            'packageAll' => $packageAll
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
        $info = $order->where(['id'=>$id])->find();

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
            }elseif ($_post['status'] == 2){
                $content = '';
                if($info['type'] == 1) {
                    $resourceInfo = model('Resource')->where(['id'=>$info['rid']])->field('id,title')->find();
                    $content = '['.$resourceInfo['title'].'] 置顶失败，操作原因（' . $_post['feedback'] . '）';
                }elseif ($info['type'] == 0){
                    $package = model('Package')->where(['id'=>$info['new_package_id']])->find();
                    $content = '购买[' . $package['title'] . '] 失败,操作原因（' . $_post['feedback'] . '）';
                }

                model('message')->allowField(false)->isUpdate(false)->save([
                    'base_type' => '1' ,
                    'subdivide_type' => '5',
                    'uid' => $info['uid'],
                    'title' => '订单审核',
                    'content' => $content,
                    'outer_id' => $info['id'],
                    'source_type' => 1,
                    'is_permanent' => 1,
                ]);
                // 审核不通过
                $state = $order->isUpdate(true)->save([
                    'status' => $_post['status'],
                    'feedback' => $_post['feedback'],
                ], ['id' => $id]);
            }
            if($state !== false) {
                return $this->success('数据提交成功');
            } else {
                return $this->error('数据提交失败');
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
        $status = request()->post('status');
        $feedback = request()->post('feedback');
        $packagePrice = model('PackagePrice');
        $resource = model('Resource');
        $packagePriceInfo = $packagePrice->find($info['package_price_id']);
        $time = time();
        $endTime = 0;
        switch ($packagePriceInfo['type']){
            case 1:
                $endTime = 86400*30;
                break;
            case 2:
                $endTime = 86400*90;
                break;
            case 3:
                $endTime = 86400*365;
                break;
        }

        $state = $resource->isUpdate(true)->allowField(true)->save([
            'top_start_time' => $time,
            'top_end_time' => $endTime + $time,
        ], ['id' => $info['rid']]);
        $order = model('order');
        $order->allowField(true)->isUpdate(true)->save([
            'feedback' => $feedback,
            'status' => $status,
        ], ['id' => $info['id']]);

        $resourceInfo = model('Resource')->where(['id'=>$info['rid']])->field('id,title')->find();
        $content = '[' . $resourceInfo['title'] . '] 置顶成功，结束时间 ' . date('Y-m-d H:i:s', $endTime + $time);
        model('message')->allowField(false)->isUpdate(false)->save([
            'base_type' => '1' ,
            'subdivide_type' => '1',
            'uid' => $info['uid'],
            'title' => '订单审核',
            'content' => $content,
            'outer_id' => $info['id'],
            'source_type' => 1,
            'is_permanent' => 1,
        ]);

        if($state !== false){
            return true;
        }
        return false;
    }

    /**
     * @param $uid
     * @param $package_id int 购买套餐id
     * @param $type
     * @param $order 订单数据
     * @return false|int
     * @author Lucius yesheng35@126.com
     */
    public function userPackage($uid, $package_id, $type, $order)
    {
        $package = model('PackageHistory');
        $userRecharge = model('userRecharge');
        $userInfo = model('userInfo');
        $packagePrice = model('packagePriceHistory');
        $orderModel = model('order');
        $userInfoData = $userInfo->where(['uid'=>$uid])->find();
        $PackageData = $package->where(['id'=>$package_id])->find();
        $userRechargeInfo = $userRecharge->where(['id' => $userInfoData['user_recharge_id']])->find();
        $packagePriceInfo = $packagePrice->where(['id'=>$order['package_price_id']])->find();
        $status = request()->post('status');
        $feedback = request()->post('feedback');
        /**
         * 购买/升级
         * 购买套餐结束时间小于结束时间，则升级，添加到期后返回降级套餐id
         * 购买套餐结束时间大于结束时间，则直接升级，不添加到期返回降级套餐id
         *
         * 续费
         * 直接添加结束时间往后延续
         *
         ***********************************************************************************
         * 套餐开通正常开通就好
         * 套餐续费
         * start_time取上一条记录的start_time
         * pay_price=购买的套餐价格+上一条记录的pay_price

         * 套餐升级
         * start_time也取上一条记录的start_time
         * end_time根据购买的升级套餐时间是一个月还是一个季度，以start_time为开始时间加30天或90天
         * pay_price=购买的套餐价格

         * 套餐补加
         * 举例：一个用户开通了三个月初级vip后，又购买了一个月的高级vip
         * 新的高级套餐正常开通allot_recharge_id记录新生成的三个月初级vip的记录
         * 新生成的初级vip套餐信息如下:
         * 套餐开通时间=旧初级套餐开通时间
         * 套餐结束时间=旧初级套餐结束时间+一个月时间(不一定是30天，如果补加的套餐是一个季度就加90天)
         * pay_price=旧初级套餐的pay_price
         * 如果新的高级套餐进行续费，则在审核时 新生成的初级vip的结束时间也要对应的增加
         ***********************************************************************************
         */

        $time = time();
        $start_time = $time;
        $endTime = 0;
        $title = '';

        switch ($packagePriceInfo['type']){
            case 1:
                $endTime =+ 86400*30;
                $title = '1个月';
                break;
            case 2:
                $endTime =+ 86400*90;
                $title = '1个季度';
                break;
            case 3:
                $endTime =+ 86400*365;
                $title = '1年';
                break;
        }

        $recharge_id = 0;

        $orderModel->allowField(true)->isUpdate(true)->save([
            'feedback' => $feedback,
            'status' => $status,
        ], ['id'=>$order['id']]);

        if($type == 0){
            // 0 购买
            $end_time = $time + $endTime;
            if($userRechargeInfo['allot_recharge_id'] > 0) {
                $recharge_id = $userRechargeInfo['allot_recharge_id'];
                $userRechargeOne = $userRecharge->where(['id'=>$recharge_id])->find();
                $userRecharge->allowField(true)->isUpdate(true)->save([
                    'end_time' => $endTime + $userRechargeOne['end_time'],
                ], ['id'=>$recharge_id]);
            }

            $userRecharge->allowField(true)->isUpdate(false)->save([
                'uid' => $uid,
                'package_id' => $package_id,
                'pay_price' => $packagePriceInfo['new_amount'],
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
                'start_time' => $time, // 开始时间
                'end_time' => $end_time , // 结束时间
                'remarks' => '审核-购买套餐 ',
            ]);
            $userRecharge_id = $userRecharge->id;

            $save3 = [
                'base_type' => '1' ,
                'subdivide_type' => '4',
                'uid' => $order['uid'],
                'title' => '订单审核',
                'content' => "您购买了".$title."灰度[" . $PackageData['title'] . "]，当前[" . $PackageData['title'] . "]到期时间" . date('Y-m-d H:i:s', $end_time),
                'outer_id' => $order['id'],
                'source_type' => 1,
                'is_permanent' => 1,
            ];
            model('message')->saveId($save3);

            return $userInfo->allowField(true)->isUpdate(true)->save(['user_recharge_id'=>$userRecharge_id], ['uid'=>$uid]);
        }elseif ($type== 1) {
            // 续费
            if($userRechargeInfo['allot_recharge_id'] > 0) {
                $recharge_id = $userRechargeInfo['allot_recharge_id'];
                $userRechargeOne = $userRecharge->where(['id'=>$recharge_id])->find();
                $userRecharge->allowField(true)->isUpdate(true)->save([
                    'end_time' => $endTime + $userRechargeOne['end_time'],
                ], ['id'=>$recharge_id]);
            }
            $userRecharge->saveId([
                'uid' => $uid,
                'package_id' => $package_id,
                'pay_price' => $packagePriceInfo['new_amount'] + $userRechargeInfo['pay_price'],
                'allot_recharge_id' => $recharge_id,
                'flush' => $PackageData['flush'],
                'publish' => $PackageData['publish'],
                'view_demand' => $PackageData['view_demand'],
                'view_provide' => $PackageData['view_provide'],
                'view_provide_give' => $PackageData['view_provide_give'],
                'used_flush' => $userRechargeInfo['used_flush'],
                'used_publish' => $userRechargeInfo['used_publish'],
                'used_view_demand' => $userRechargeInfo['used_view_demand'],
                'used_view_provide' => $userRechargeInfo['used_view_provide'],
                'start_time' => $userRechargeInfo['start_time'], // 开始时间
                'end_time' => $userRechargeInfo['end_time'] + $endTime, // 结束时间
                'remarks' => '审核-续费套餐 ',
            ]);
            $userRecharge_id = $userRecharge->id;
            $save3 = [
                'base_type' => '1' ,
                'subdivide_type' => '4',
                'uid' => $order['uid'],
                'title' => '订单审核',
                'content' => "您续费了".$title."灰度[" . $PackageData['title'] . "]，当前[" . $PackageData['title'] . "]到期时间" . date('Y-m-d H:i:s', $userRechargeInfo['end_time'] + $endTime),
                'outer_id' => $order['id'],
                'source_type' => 1,
                'is_permanent' => 1,
            ];
            model('message')->saveId($save3);
            return $userInfo->allowField(true)->isUpdate(true)->save(['user_recharge_id'=>$userRecharge_id], ['uid'=>$uid]);
        }elseif ($type == 2){
            // 2 升级
            //$end_time = $time + $endTime;
            if (($time + $endTime) < $userRechargeInfo['end_time'] && $userRechargeInfo['package_id'] != 1) {
                $t = $userRechargeInfo['end_time'] - $time;
                $save1 = [
                    'uid'               => $uid,
                    'package_id'        => $package_id,
                    'pay_price'         => $packagePriceInfo['new_amount'],
                    'flush'             => $PackageData['flush'],
                    'publish'           => $PackageData['publish'],
                    'view_demand'       => $PackageData['view_demand'],
                    'view_provide'      => $PackageData['view_provide'],
                    'view_provide_give' => $PackageData['view_provide_give'],
                    'used_flush'        => $userRechargeInfo['used_flush'],
                    'used_publish'      => $userRechargeInfo['used_publish'],
                    'used_view_demand'  => $userRechargeInfo['used_view_demand'],
                    'used_view_provide' => $userRechargeInfo['used_view_provide'],
                    'start_time'        => $userRechargeInfo['start_time'], // 开始时间
                    'end_time' => $userRechargeInfo['end_time'] + $endTime, // 结束时间
                    'remarks'           => '审核-升级套餐-原套餐 ',
                ];
                $userRecharge->allowField(true)->data($save1, true)->save();
                $recharge_id = $userRecharge->id;
            }

            $save2 = [
                'uid'               => $uid,
                'package_id'        => $package_id,
                'pay_price'         => $packagePriceInfo['new_amount'],
                'allot_recharge_id' => $recharge_id,
                'flush'             => $PackageData['flush'],
                'publish'           => $PackageData['publish'],
                'view_demand'       => $PackageData['view_demand'],
                'view_provide'      => $PackageData['view_provide'],
                'view_provide_give' => $PackageData['view_provide_give'],
                'used_flush'        => 0,
                'used_publish'      => $userRechargeInfo['used_publish'],
                'used_view_demand'  => 0,
                'used_view_provide' => 0,
                'start_time'        => $time, // 开始时间
                'end_time'          => $time + $endTime, // 结束时间
                'remarks'           => '审核-升级套餐 ',
                'create_time' => 0,
                'update_time' => 0,
            ];

            $userRecharge->allowField(true)->isUpdate(false)->data($save2, true)->save();

            $save3 = [
                'base_type' => '1' ,
                'subdivide_type' => '4',
                'uid' => $order['uid'],
                'title' => '订单审核',
                'content' => "您成功升级了".$title."灰度[" . $PackageData['title'] . "]，当前[" . $PackageData['title'] . "]到期时间" . date('Y-m-d H:i:s', $time + $endTime),
                'outer_id' => $order['id'],
                'source_type' => 1,
                'is_permanent' => 1,
            ];
            model('message')->saveId($save3);

            $userRecharge_id = $userRecharge->id;
            return $userInfo->allowField(true)->isUpdate(true)->save(['user_recharge_id'=>$userRecharge_id], ['uid'=>$uid]);
        }else {
            return false;
        }

    }

}