<?php
namespace app\admin\controller;
use think\Controller;
use think\Loader;
use util\GetMacAddr;

class Login extends Controller
{
    public function index()
    {
    	return view();
    }

    public function login()
    {
        if (request()->isPost()) {
            $model  = model('Admin');
            $data   = request()->post();
            $isUser = $model->where(['username' => $data['username'], 'status'=>1])->find();
            if(!empty($isUser)){
                if($isUser['password'] !== md5(md5($data['password']).$isUser['str'])){
                    return error_json(lang('LoginFail'));
                }

                //设置登陆str
//                $number = GetRandStr(12);
//                setLoginStr($number);
//                $model->where(array('id'=>$isUser['id']))->update(array('str'=>$number));
                setLoginUserId($isUser['id']);
                setLoginUserStr($isUser['str']);
                getAdminLog("用户登录");
                return success_json(lang("LoginSuccess"), ['href'=>'http://'.$_SERVER['HTTP_HOST']]);
            }else{
                return error_json(lang('LoginFail'));
            }
        }
    }

    public function cc()
    {
        $mac = new GetMacAddr();
        var_dump($mac->GetMacAddr(PHP_OS));
    }
}