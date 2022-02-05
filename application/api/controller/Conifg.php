<?php

namespace app\api\controller;

// 制定允许其他域名访问
header('Access-Control-Allow-Origin:*');
// 响应类型
header('Access-Control-Allow-Methods:*');
//请求头
header('Access-Control-Allow-Headers:*');
// 响应头设置
header('Access-Control-Allow-Credentials:true');
class Conifg
{
    public function getAppid(){
        $appid = getConfig('appid');
        $return = [
            'appid'=>$appid,
            'url'=>"http://xxxxx.top/h5"
        ];
        return_success($return);
    }
}