<?php

namespace app\api\controller;

use think\Db;
use think\exception\ErrorException;
// 制定允许其他域名访问
header('Access-Control-Allow-Origin:*');
// 响应类型
header('Access-Control-Allow-Methods:*');
//请求头
header('Access-Control-Allow-Headers:*');
// 响应头设置
header('Access-Control-Allow-Credentials:true');

class Notify
{
    public function wxPayNotify()
    {
        $xml = file_get_contents("php://input");
        $arr = $this->xml_to_data($xml);
        if ($arr['result_code'] == 'SUCCESS' && $arr['return_code'] == 'SUCCESS') {
            $out_trade_no = $arr['out_trade_no']; //商户订单号
            $pay_time = date('Y-m-d H:i:s', time()); //支付时间
            $where = ['out_trade_no' => $out_trade_no, 'status' => 0];
            $order = Db::table('cg_pay_order')->where($where)->find();
            if ($order) {
                $user = Db::table('cg_user')->where(['id'=>$order['user_id']])->find();
                $start_time = $user['expiration_time'] > time() ? $user['expiration_time'] : time();
                // 1 开通vip  2 充值账户
                if($arr['attach'] == 1){
                    $user_update = [
                        'vip_status' => 1,
                        'expiration_time'=>$start_time + 60*60*24*365
                    ];
                    $order_update = [
                        'pay_time' => $pay_time,
                        'status' => 1,
                    ];
                    try {
                        Db::startTrans();
                        $succ_1 = Db::table('cg_user')->where(['id'=>$order['user_id']])->update($user_update);
                        $succ_2 = Db::table('cg_pay_order')->where(['out_trade_no' => $out_trade_no])->update($order_update);
                        $insert = ['desc'=>'开通vip','create_time'=>time(),'total_free'=>'微信支付'.$order['total_fee'].'元','user_id'=>$order['user_id']];
                        $record = Db::table('cg_account_record')->insertGetId($insert);
                        if ($succ_1 !== false && $succ_2 !== false && $record >= 1) {
                            Db::commit();
                            echo "<xml>
                          <return_code><![CDATA[SUCCESS]]></return_code>
                          <return_msg><![CDATA[OK]]></return_msg>
                        </xml>";
                            exit;
                        }
                        Db::rollback();
                    } catch (ErrorException $e) {
                        Db::rollback();
                    }

                }elseif($arr['attach'] == 2){
                    $pay_rule_id = -1;
                    $add_money = 0;
                    $rule_list = Db::query("select * from cg_pay_rule order by min_pay desc ");
                    foreach ($rule_list as $value) {
                        if ($order['total_fee'] >= $value['min_pay']) {
                            $add_money = $value['give_money'];
                            $pay_rule_id = $value['id'];
                            break 1;
                        }
                    }
                    $update = [
                        'pay_time' => $pay_time,
                        'status' => 1,
                        'pay_rule_id' => $pay_rule_id,
                    ];
                    try {
                        Db::startTrans();
                        $succ_1 = Db::table('cg_pay_order')->where($where)->update($update);
                        $add_money = $order['total_fee'] + $add_money;
                        $succ_2 = Db::query("update cg_user set money=money+$add_money where id = {$order['user_id']}");
                        $user = Db::table('cg_user')->where(['id'=>$order['user_id']])->find();
                        $insert = ['desc'=>'平台充值','create_time'=>time(),'total_free'=>'+'.$order['total_fee'].'元，余额'.$user['money'].'元','user_id'=>$order['user_id']];
                        $record = Db::table('cg_account_record')->insertGetId($insert);
                        if ($succ_1 !== false && $succ_2 !== false && $record >= 1) {
                            Db::commit();
                            echo "<xml>
                          <return_code><![CDATA[SUCCESS]]></return_code>
                          <return_msg><![CDATA[OK]]></return_msg>
                        </xml>";
                            exit;
                        }
                        Db::rollback();
                    } catch (ErrorException $e) {
                        Db::rollback();
                    }
                }
            }
        }
    }

    public function xml_to_data($xml)
    {
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
    }
}