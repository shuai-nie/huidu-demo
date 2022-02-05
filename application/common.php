<?php
// 获取公共参数
function getConfig($key){
    $res =  \think\Db::query("select `value` from cg_config where `key` = '$key'");
    if(empty($res[0])){
        return '';
    }
    return  $res[0]['value'];
}

// 获取用户id
function getLoginUserId()
{
    return cookie('user_id_with_app');
}

// 设置用户id
function setLoginUserId($user_id)
{
    cookie('user_id_with_app', $user_id);
}

// 获取用户id
function getBoenUserId()
{
    return cookie('boen_user_id');
}

// 获取用户前端id
function getBoenUserName()
{
    return cookie('boen_user_name');
}

// 设置用户权限
function setRules($rules)
{
    cookie('boen_rules', $rules);
}

// 获取用户权限
function getRules()
{
    return cookie('boen_rules');
}

/**
 * 获取随机字符串
 * @param $length int 长度
 * @return string 随机字符串
 */
function GetRandStr($length)
{
    $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $len = strlen($str) - 1;
    $randstr = '';
    for ($i = 0; $i < $length; $i++) {
        $num = mt_rand(0, $len);
        $randstr .= $str[$num];
    }
    return $randstr;
}

// 登陆 设置str
function setLoginStr($str)
{
    cookie('loginStr', $str);
}

// 登陆 获取str
function getLoginStr()
{
    return cookie('loginStr');
}

// 登陆 设置str
function setClassLoginStr($str)
{
    cookie('loginClassStr', $str);
}

// 登陆 获取str
function getClassLoginStr()
{
    return cookie('loginClassStr');
}

// 返回成功
function success_callback($msg = "返回成功", $returndata = [], $code = 200)
{
    echo json_encode([
        'msg' => $msg,
        'data' => $returndata,
        'code' => $code
    ]);
    exit;
}

// 返回失败
function error_callback($msg = "返回失败", $returndata = [], $code = 400)
{
    echo json_encode([
        'msg' => $msg,
        'data' => $returndata,
        'code' => $code
    ]);
    exit;
}

/*检测权限*/
function checkAuth($url)
{
    $Jurisdiction = new lib\Jurisdiction();
    return $Jurisdiction->check($url);
}

function getToken($appid, $appsecret)
{
    $file = "token.txt";
    {
        $token = file_get_contents($file);
        $token = json_decode($token, true);
        if (empty($token['access_token']) || time() - $token['expires_in'] > 7000) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
            $token = GetHttp($url);
            $token = json_decode($token, true);
            $token['expires_in'] = time();
            file_put_contents("token.txt", json_encode($token));
            return $token['access_token'];
        } else {
            return $token['access_token'];
        }
    }

}



/**
 * get
 * @param string $url 请求地址
 */
function GetHttp($url)
{
    // 关闭句柄
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
    $tmpInfo = curl_exec($curl); //返回api的json对象
    if (curl_exec($curl) === false) {
        return 'Curl error: ' . curl_error($ch);
    }
    //关闭URL请求
    curl_close($curl);
    return $tmpInfo; //返回json对象
}

/**
 * 模拟post进行url请求
 * @param string $postUrl
 * @param string $param
 */
function request_post($postUrl = '', $param = '')
{
    if (empty($postUrl) || empty($param)) {
        return false;
    }

    $curlPost = $param;
    $ch = curl_init();//初始化curl
    curl_setopt($ch, CURLOPT_URL, $postUrl);//抓取指定网页
    curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
    curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
    $data = curl_exec($ch);//运行curl
    curl_close($ch);

    return $data;
}