<?php

namespace app\api\controller;
use think\Controller;
use think\Db;

// 制定允许其他域名访问
header('Access-Control-Allow-Origin:*');
// 响应类型
header('Access-Control-Allow-Methods:*');
//请求头
header('Access-Control-Allow-Headers:*');
// 响应头设置
header('Access-Control-Allow-Credentials:true');
class Base extends Controller
{
    protected $user;
	public function _initialize()
	{
	    $token = !empty($_SERVER['HTTP_TOKEN']) ? $_SERVER['HTTP_TOKEN'] : '';
        if(!empty($token)){
            $user = Db::table('cg_user')->where(['token'=>$token])->find();
            if(empty($user)){
                return_error('请登录',401);
            }
            $this->user = $user;
        }else{
            return_error('请登录',401);
        }
	}
}