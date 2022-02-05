<?php
/**
 * Created by PhpStorm.
 * User: wangfeng
 * Date: 2018/4/28
 * Time: 上午10:30
 */

namespace app\common\sms;


class UserSms
{
    static private $sign = '亟亟城运';

    /**
     *  注册 - 验证码短信
     * 模版内容: 您正在申请手机注册，验证码为：${code}，5分钟内有效！
     * @param $phone
     * @param $message
     */
    static public function code($phone, $message)
    {
        return Sms::sendSms($phone, self::$sign, 'SMS_152165006', $message);
    }

    /**
     *  找回密码 - 验证码短信
     * 模版内容: 您的动态码为：${code}，您正在进行密码重置操作，如非本人操作，请忽略本短信！
     * @param $phone
     * @param $message
     */
    static public function retrievePassCode($phone, $message)
    {
        return Sms::sendSms($phone, self::$sign, 'SMS_152425921', $message);
    }
}