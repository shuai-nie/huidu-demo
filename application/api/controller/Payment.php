<?php

namespace app\api\controller;

use think\Db;
use Yansongda\Pay\Pay;
use Yansongda\Pay\Log;

// 制定允许其他域名访问
header('Access-Control-Allow-Origin:*');
// 响应类型
header('Access-Control-Allow-Methods:*');
//请求头
header('Access-Control-Allow-Headers:*');
// 响应头设置
header('Access-Control-Allow-Credentials:true');
class Payment extends Base
{
    protected $config;

    public function __construct()
    {
        $this->config = [
            'appid' => 'wxb3fxxxxxxxxxxx', // APP APPID
            'app_id' => getConfig('appid'), // 公众号 APPID
            'miniapp_id' => 'wxb3fxxxxxxxxxxx', // 小程序 APPID
            'mch_id' => getConfig('mch_id'),
            'key' => getConfig('key'),
            'notify_url' => $_SERVER['HTTP_ORIGIN'] . '/api/Notify/wxPayNotify'
        ];
    }

    public function wxPay()
    {
        $user = is_login();
        $params = input('post.');
        if (empty($params['pay_type']) || empty($params['openid']) ) {
            return_error("支付错误[pay_type openid ]");
        }
        $rule_id = !empty($params['rule_id']) ? $params['rule_id'] : -1;
        if ($params['pay_type'] == 1 ) {
            $body = "锅炉采购-开通平台vip";
            $pay_money = getConfig('vip_money');
            if(empty($pay_money) || $pay_money*100 < 1){
                return_error('平台设置错误');
                exit;
            }
        } elseif ($params['pay_type'] == 2) {
            $body = "锅炉采购-账户充值";
        } else {
            return_error("支付方式错误[pay_type]");
        }

        if ($rule_id != -1 && empty($params['pay_money'])) {
            $rule = Db::table("cg_pay_rule")->where(['id' => $rule_id])->find();
            $pay_money = $rule['min_pay'];
        } elseif (!empty($params['pay_money']) && $params['pay_type'] == 2) {
            $pay_money = $params['pay_money'];
        }

        if (empty($pay_money)) {
            return_error("支付金额错误[pay_money]");
        }
        $order_no = date('YmdH') . mt_rand(1111, 9999) . mt_rand(111, 999);
        $insert_arr = [
            'user_id'=>$user['id'],
            'pay_rule_id'=>$rule_id,
            'out_trade_no'=>$order_no,
            'total_fee'=>$pay_money,
            'pay_type'=>$params['pay_type'],
            'status'=>0,
            'create_time'=>time(),
        ];
        Db::table('cg_pay_order')->insertGetId($insert_arr);

        $total_fee = $pay_money * 100;
        $order = [
            'out_trade_no' => $order_no,
            'total_fee' => $total_fee,
            'body' => $body,
            'openid' => $params['openid'],
            'attach' => $params['pay_type'],
        ];
        $pay = Pay::wechat($this->config)->mp($order);
        return_success($pay);

    }
}