<?php

use think\Cache;
use think\Db;

//error_reporting(E_ERROR | E_PARSE );
/**
 * [获取论团用户信息]
 * @param $uid
 * @return array
 * @author Lucius yesheng35@126.com
 */
//function CacheMember($uid) {
//    $key = 'CommonMember' . $uid;
//    if($CacheMember = Cache::get($key)) {
//        return $CacheMember;
//    } else {
//        $db2 = Db::connect(config('db2'));
//        $result = $db2->name('common_member')->where(['uid'=>$uid])->field('uid,username')->find();
//        Cache::set($key, $result, 3600);
//        return $result;
//    }
//}

/**
 * [获取用户信息]
 * @param $uid
 * @return array
 * @author Lucius yesheng35@126.com
 */
function CacheUser($uid) {
    $key = "User_". $uid;
    if($CacheUser = Cache::get($key)) {
        return $CacheUser;
    } else {
        $result = model('User')->find($uid);
        Cache::set($key, $result, 3600);
        return $result;
    }
}

function CacheResource($id) {
    $key = 'CacheResource' . $id;
    if($CacheMember = Cache::get($key)) {
        return $CacheMember;
    } else {
        $result = model('Resource')->where(['id'=>$id])->find();
        Cache::set($key, $result, 60);
        return $result;
    }
}


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
    return session('user_id_with_app');
}

// 设置用户id
function setLoginUserId($user_id)
{
    session('user_id_with_app', $user_id);
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

/**
 * 成功
 * User: yesheng35@126.com
 * DateTime 2022/2/7 15:12
 * @param string $msg
 * @param array $returndata
 * @param int $code
 * @return \think\response\Jsons
 */
function success_json($msg = "返回成功", $returndata = [], $code = 200)
{
    return json([
        'msg' => $msg,
        'data' => $returndata,
        'code' => $code
    ]);
}

/**
 * 失败
 * User: yesheng35@126.com
 * DateTime 2022/2/7 15:12
 * @param string $msg
 * @param array $returndata
 * @param int $code
 * @return \think\response\Json
 */
function error_json($msg = "返回失败", $returndata = [], $code = 400)
{
    return json([
        'msg' => $msg,
        'data' => $returndata,
        'code' => $code
    ]);
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

/**
 * 删除目录及文件
 * @param  deletefile //删除文件夹及里面的所有数据
 * @param  dirName  // 基于 ./Public/Uploads/ 下面的路径
 * @param  is_dir  //判断是否是目录
 * @param  opendir  // 打开文件夹
 * @param  readdir // 读取文件夹
 */
function deletefile($dirName)
{
    $dir = $dirName;
    if (is_dir(iconv('utf-8', 'gb2312', $dir)) == true) {
        if ($handle = opendir(iconv('utf-8', 'gb2312', $dir))) {//打开文件内容
            while (false !== ($files = readdir($handle))) {//读取文件内容
                $files = iconv('gb2312', 'utf-8', $files);
                if ($files != "." && $files != "..") {
                    $files   = iconv('utf-8', 'utf-8', $files);//字符转义
                    $dir     = iconv('utf-8', 'utf-8', $dir);
                    $new_dir = $dir . "/" . $files;
                    if (is_dir(iconv('utf-8', 'gb2312', $new_dir))) {
                        deletefile($new_dir);//递归调用
                    } else {
                        //删除pdf 里面的转换文件
                        $zip_type = substr(strrchr(iconv('utf-8', 'gb2312', $files), '.'), 1);
                        if ($zip_type == 'xls' || $zip_type == 'xlsx' || $zip_type == 'docx' || $zip_type == 'doc') {
                            $filename_file = $files;
                            $houzhui_file  = substr(strrchr($filename_file, '.'), 1);
                            $wei_file      = mb_strlen($houzhui_file, 'utf-8') + 1;//获取后缀名的长度
                            $zong_file     = mb_strlen($filename_file, 'utf-8');//获取总的长度
                            $filenams_file = mb_substr($filename_file, 0, $zong_file - $wei_file, 'utf-8');
                            $pdfname_file  = $filenams_file;//截取文件名前缀
                            unlink(iconv('utf-8', 'gb2312', './Public/Uploads/pdf/' . $pdfname_file . '.pdf'));
                        }
                        unlink(iconv('utf-8', 'gb2312', $new_dir));
                    }
                }
            }

            closeDir($handle);

            if (rmdir(iconv('utf-8', 'GBK', $dir))) {
                $value['file']    = '成功删除目录';
                $value['success'] = 'success';
                //echo "成功删除目录";
            }
        } else {
            // echo '打开不了目录';
            $value['file'] = '打开不了目录';
            $value['fail'] = 'fail';
        }
    } else {
        if (is_file(iconv('utf-8', 'gb2312', $dir)) == false) {
            //echo '没有找到目录';
            $value['file'] = '没有找到目录';
            $value['fail'] = 'fail';
        }
    }
    return $value;
}


function getRealIP(){
    $forwarded = request()->header("x-forwarded-for");
    if($forwarded){
        $ip = explode(',',$forwarded)[0];
    }else{
        $ip = request()->ip();
    }
    return $ip;
}

function getPriceType($t)
{
    switch ($t){
        case 1: return '月';
        case 2: return '季';
        case 3: return '年';
        default: return '-';
    }
}