<?php

namespace app\api\controller;

use think\Db;

class User extends Base
{
    public function userInfo()
    {
        $user = $this->user;
        if(!empty($user['province_list'])){
            $user['province_list'] = explode(',',$user['province_list']);
        }
        if(!empty($user['expiration_time'])){
            $user['expiration_time'] = date("Y-m-d H:i:s",$user['expiration_time']);
        }
        return_success($user);
    }

    public function saveUserProvince()
    {
        $post = input('post.');

        if(!in_array($post['is_jieshou'],[0,1]) || empty($post['province_list']) || gettype($post['province_list']) != 'string'){
            return_error('参数错误');
        }
        $update = [
            'is_jieshou'=>$post['is_jieshou'],
            'province_list'=>$post['province_list'],
        ];
        $success = Db::table('cg_user')->where(['id'=>$this->user['id']])->update($update);
        if($success !== false){
            return_success();
        }
        return_error('操作失败');
    }
    //账户明细
    public function user_account_record(){
        $user = $this->user;
        $params = input('input.');
        $page = !empty($params['page']) ? $params['page'] : 1;
        $page_size = !empty($params['page_size']) ? $params['page_size'] : 15;
        $limit = ($page-1)*$page_size.",$page_size";
        $list = Db::query("select * from cg_account_record where user_id = {$user['id']} order by create_time desc limit $limit");
        foreach ($list as $key => $value){
            $list[$key]['create_time'] = date('Y-m-d H:i:s',$value['create_time']);
        }
        return_success($list);
    }
}