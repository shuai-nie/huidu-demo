<?php
namespace app\admin\controller;
use think\Controller;
use think\Loader;
use util\GetMacAddr;
use think\captcha\Captcha;

define('NOW_TIME', time());

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

            $config =    [
                'fontSize' => 120,
                'length' => 4,
                'useNoise' => false,
                'fontttf' => '2.ttf',
                'codeSet' => '1234567890',
            ];
            $captcha = new Captcha($config);
            $IsCaptcha = $captcha->check($data['yzm'], 1234);
            if($IsCaptcha === false){
                return error_json('验证码错误');
            }

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
        $mac = new GetMacAddr();// PHP_OS
        var_dump($mac->GetMacAddr('window'));
    }

    public function yzm()
    {
        $config =    [
            'fontSize' => 120,
            'length' => 4,
            'useNoise' => false,
            'fontttf' => '2.ttf',
            'codeSet' => '1234567890',
        ];
        $captcha = new Captcha($config);
        return $captcha->entry(1234);
    }

}