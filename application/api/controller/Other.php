<?php

namespace app\api\controller;

use think\Db;

// 制定允许其他域名访问
header('Access-Control-Allow-Origin:*');
// 响应类型
header('Access-Control-Allow-Methods:*');
//请求头
header('Access-Control-Allow-Headers:*');
// 响应头设置
header('Access-Control-Allow-Credentials:true');
class Other
{
    // 充值规则
    public function getPayRule(){
        $list = Db::query("select * from cg_pay_rule order by sort desc");
        return_success($list);
    }
    //参数接口
    public function getConfig(){
        $params = input('post.');
        if(empty($params['key'])){
            return_error('参数错误');
        }
        return_success(getConfig($params['key']));
    }
    //省份列表
    public function province_list(){
        $province_list = Db::table('cg_province')->select();
        return_success($province_list);
    }
}