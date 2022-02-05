<?php

namespace app\api\controller;

use think\Db;

class Order extends Base
{
    //添加订单
    public function addOrder()
    {
        $params = input('post.');
        if (empty($params['resources_id']) || empty(intval($params['buy_num']))) {
            return_error('请求错误');
        }
        $pay_num = intval($params['buy_num']);
        if($pay_num < 1){
            return_error('购买次数错误，请重试');
        }
        $user = $this->user;
        //查看用户是否有余额进行支付
        $resources = Db::table('cg_resources')->where(['id'=>$params['resources_id']])->find();
        $vip_end_time = getconfig('vip_time');
        $vip_end_time = $resources['create_time'] + $vip_end_time * 60;
        if(time() < $vip_end_time && $user['vip_status'] != 1){
            return_error('当前时间是vip购买时间，截止时间是'.date("Y-m-d H:i:s",$vip_end_time));
        }
        if(empty($resources)){
            return_error('商机不存在');
        }
        if($resources['shengyu_num'] == 0){
            return_error('他们的手太快了，商机已被抢完了');
        }
        if($resources['shengyu_num'] < $pay_num ){
            return_error('商机超过购买次数限制');
        }
        $money = $pay_num * $resources['money'];
        if($user['money'] < $money){
            return_error('余额不足~请充值');
        }

        $insert_arr = [
            'resources_id' => $params['resources_id'],
            'user_id' => $user['id'],
            'create_time' => time(),
            'buy_num' => $params['buy_num'],
            'pay_money' => $money,
            'status' => 1,
        ];
        Db::startTrans();
        // 减商机可购买次数
        $succ_1 = Db::query("update cg_resources set shengyu_num = shengyu_num - $pay_num where id = {$params['resources_id']}");
        //减去用户余额
        $succ_2 = Db::query("update cg_user set money = money - $money where id = {$user['id']} ");

        //插入订单
        $succ_3 = Db::table('cg_resources_order')->insertGetId($insert_arr);


        $insert = ['desc'=>'购买商机','create_time'=>time(),'total_free'=>'-'.$money.'元，余额'.($user['money']-$money).'元','user_id'=>$user['id']];
        $record = Db::table('cg_account_record')->insertGetId($insert);

        if($succ_1 !== false && $succ_2 !== false && $succ_3 >= 1 && $record >= 1){
            Db::commit();
            return_success('购买成功');
        }
        Db::rollback();
        return_error('购买失败');
    }

    //用户订单列表
    public function orderList()
    {
        $params = input('post.');
        $page = !empty($params['page']) ? $params['page'] : 1;
        $page_size = !empty($params['page_size']) ? $params['page_size'] : 15;
        $limit = ($page - 1) * $page_size . ",$page_size";
        $user = $this->user;
        $list = Db::query("select *,t1.create_time as order_create_time,t1.id as order_id,t2.id as resources_id from cg_resources_order as t1 left join cg_resources as t2 on (t1.resources_id = t2.id )where t1.user_id = {$user['id']} and t1.status = 1 order by t1.create_time desc limit $limit");
        $count = Db::query("select count(*) as count from cg_resources_order where user_id = {$user['id']} and status = 1");
        if (!empty($count[0]['count'])) {
            $total = $count[0]['count'];
        } else {
            $total = 0;
        }
        foreach ($list as $key => $value) {
            $list[$key]['tuikuan'] = 0;
            if( time() - $value['order_create_time'] < 60*60*24){
                $list[$key]['tuikuan'] = 1;
            }
            $list[$key]['order_create_time'] = date("Y-m-d H:i:s", $value['order_create_time']);
        }
        return_success(['list' => $list, 'total' => $total]);
    }

    //商机退款申请
    public function refund_record_apply()
    {
        $params = input('post.');
        if (empty($params['resources_id']) || empty($params['resources_order_id'])) {
            return_error('参数错误');
        }
        $user_id = $this->user['id'];
        //查看订单是否存在
        $order_exist = Db::table('cg_resources_order')->where(['id' => $params['resources_order_id'], 'user_id' => $user_id, 'status' => 1])->find();
        if (empty($order_exist)) {
            return_error('订单不存在');
        }
        $insert_arr = [
            'resources_id' => $params['resources_id'],
            'resources_order_id' => $params['resources_order_id'],
            'user_id' => $user_id
        ];
        //查询是否已经提交过了
        $exist = Db::table('cg_refund_record')->where($insert_arr)->find();
        if (!empty($exist['id'])) {
            return_success(1, '您已经提交过了', 201);
        }
        $insert_arr['create_time'] = time();
        Db::table('cg_refund_record')->insert($insert_arr);
        return_success(1, '申请成功');
    }
}