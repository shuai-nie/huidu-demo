<?php
namespace app\admin\controller;
use think\Controller;
use think\Loader;
class Login extends Controller
{
    public function index()
    {
    	return view();
    }
    public function login(){
    	$model=Loader::model('Admin');
    	$data=input('post.');
    	$isUser=$model->where(['username'=>$data['username'],'password'=>$data['password']])->find();
    	if(!empty($isUser)){
    		//设置登陆str
    		$number = GetRandStr(12);
		    setLoginStr($number);//loginStr
		    $model->where(array('id'=>$isUser['id']))->update(array('str'=>$number));
    		setLoginUserId($isUser['id']);
    		success_callback("登陆成功",['href'=>'http://'.$_SERVER['HTTP_HOST']]);
    	}else{
    		error_callback("登录失败");
    	}
    }
}