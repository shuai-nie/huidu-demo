<?php

namespace app\api\controller;

use think\Controller;
use think\Db;

class Login extends Controller
{
    public function wxlogin()
    {
        $appid = getConfig('appid');
        $appsecret = getConfig('appsecret');
//        $accessToken = getToken($appid,$appsecret);
        $params = input('post.');
        if (empty($params['code'])) { // || empty($params['user_id'])
            return_error('code 参数错误',501);
            exit;
        }
        $code = $params['code'];
        $oData = $this->getWxOpenid($appid, $appsecret, $code);
        if (empty($oData['openid'])) {
            return_error($oData);
            exit;
        }
        //查看用是否已注册
        $user_exist = Db::table('cg_user')->where(['openid' => $oData['openid']])->find();
        $token = rand_token($oData['openid']);
        if ($user_exist != false) {
            $succ = Db::table('cg_user')->where(['openid' => $oData['openid']])->update(['token'=>$token]);
            if($succ !== false){
                return_success(['openid'=>$oData['openid'],'token'=>$token]);
            }
        }

        $userInfo = $this->getWxUserInfo($oData['access_token'], $oData['openid']);
        if (empty($userInfo['openid'])) {
            return_error('获取用户信息失败');
        }
        $insert = [
            'openid' => $userInfo['openid'],
            'nickname' => $userInfo['nickname'],
            'headimgurl' => $userInfo['headimgurl'],
            'create_time' => time(),
            'province_list' => getConfig('default_province_list'),
            'is_jieshou' => 1,
            'token' => $token,
        ];
        $user_id = Db::table('cg_user')->insertGetId($insert);
        if(!empty($user_id)){
            return_success(['openid'=>$userInfo['openid'],'token'=>$token]);
        }
    }

    protected function getWxOpenid($appid, $appSecret, $code)
    {
        $request_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$appSecret&code=$code&grant_type=authorization_code";
        $oData = file_get_contents($request_url);
        $oData = json_decode($oData, true);
        if (!empty($oData['errcode'])) {
            return false;
        }
        return $oData;
    }

    protected function getWxUserInfo($userAccessToken, $openid)
    {
        $getUrl = "https://api.weixin.qq.com/sns/userinfo?access_token=" . $userAccessToken . "&openid=" . $openid . "&lang=zh_CN";
        $userInfo = file_get_contents($getUrl);
        $userInfo = json_decode($userInfo, true);
        if (empty($userInfo['openid'])) {
            return false;
        }
        return $userInfo;
    }
}