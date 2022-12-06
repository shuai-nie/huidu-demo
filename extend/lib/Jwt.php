<?php
namespace lib;

class Jwt
{
    private static $header = [
        'alg' => 'HS256',
        'typ' => 'JWT'
    ];

    private static $secret = 1234566;

    /**
     * 生成签名
     * @param array $payload 载荷
     * @return array    返回结果
     */
    public static function getToken(array $payload)
    {
        if(!is_array($payload)){
            return ['status'=>0, 'msg'=>'参数错误'];
        }

        $base_header = base64_encode(json_encode(self::$header));
        $base_payload = base64_encode(json_encode($payload));

        $sign = self::signature($base_header. $base_payload, self::$secret);
        $token = $base_header . '.' . $base_payload . '.'. $sign;
        return ['status'=>1, 'msg'=>'签名成功', 'data'=>['token'=>$token]];
    }


    /**
     * 验证签名
     * @param $token    签名token
     * @return array|void   返回结果
     */
    public static function verifyToken($token)
    {
        $tokens = explode('.', $token);
        if(count($tokens) != 3){
            return ['status'=>0, 'mag'=>'字节数不对'];
        }

        list($base_header, $base_payload, $base_sign) = $tokens;
        $base_header_verigy = (array)json_decode(base64_decode($base_header));
        if(empty($base_header_verigy['alg'])){
            return ['status'=>0, 'msg'=>'alg不存在'];
        }

        $base_payload_verify = (array)json_decode(base64_decode($base_payload));
        if(!isset($base_payload_verify['iat']) || $base_payload_verify['iat'] > time() ) {
            return ['status'=>0, 'msg'=>'签发时间大于当前服务器时间验证失败'];
        }
        if(!isset($base_payload_verify['exp']) || $base_payload_verify['exp'] < time()) {
            return ['status' => 0, 'msg'=>'签名过期'];
        }
        $sign = self::signature($base_header. $base_payload, self::$secret);
        if($base_sign != $sign) {
            return ['status'=>0, 'msg'=>'验证失败'];
        }
        return ['status'=>1, 'msg'=>'验证通过'];
    }
    /**
     * 签证
     * @param $input    header 和 payload
     * @param $secret   密钥
     * @param $alg      加密方式
     * @return string   返回签证字符串
     */
    public static function signature($input, $secret, $alg = 'HS256')
    {
        $sign_md5 = $input . $secret . $alg;
        return md5($sign_md5);
    }
}

$payload = array('user'=>'xiaomin', 'iat'=>time(), 'exp'=>time()+3600);
$sign = Jwt::getToken($payload);
print_r($sign);
$string = Jwt::verifyToken($sign['data']['token']);
print_r($string);
