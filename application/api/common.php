<?php

use think\Db;

function return_success($data = [], $message = 'SUCCESS', $code = 200)
{
    echo json_encode(array(
        'code' => $code,
        'message' => $message,
        'data' => $data
    ));
    exit;
}

function return_error($message = 'ERROR', $code = 500)
{
    echo json_encode(array(
        'code' => $code,
        'message' => $message
    ));
    exit;
}

function rand_token($text)
{
    return md5($text . mt_rand(100000, 999999));
}

function is_login()
{
    if (!empty($_SERVER['HTTP_TOKEN'])) {
        $token = $_SERVER['HTTP_TOKEN'];
        $user = Db::table("cg_user")->where(['token' => $token])->find();
        if (empty($user)) {
            return_error('', 401);
            exit;
        }
        return $user;
    } else {
        return_error('', 401);
    }
}