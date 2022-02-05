<?php

namespace app\api\controller;

// 制定允许其他域名访问
use think\Db;

header('Access-Control-Allow-Origin:*');
// 响应类型
header('Access-Control-Allow-Methods:*');
//请求头
header('Access-Control-Allow-Headers:*');
// 响应头设置
header('Access-Control-Allow-Credentials:true');

class Resources extends Base
{
    //商机列表
    public function resourcesList()
    {
        $params = input('post.');
        $page = !empty($params['page']) ? $params['page'] : 1;
        $page_size = !empty($params['page_size']) ? $params['page_size'] : 15;
        $limit = ($page - 1) * $page_size . ",$page_size";
        $where = " id >= 1 ";
        if(!empty($params['title'])){
            $where .= " and title like '%{$params['title']}%' ";
        }
        $sql = "select * from cg_resources where $where order by create_time desc limit $limit";
        $list = Db::query($sql);
        foreach ($list as $k => $v) {
            unset($list[$k]['phone']);
            $list[$k]['create_time'] = date('m-d H:i', $v['create_time']);
        }
        return_success($list);
    }

    //商机详情
    public function resourcesInfo()
    {
        $params = input('post.');
        if (empty($params['id'])) {
            return_error('参数错误');
        }
        $info = Db::table('cg_resources')->where(['id' => $params['id']])->find();
        //返回剩余购买次数 展示vip购买时间
        $vip_end_time = getconfig('vip_time');
        $vip_end_time = $info['create_time'] + $vip_end_time * 60;
        $info['vip_end_timestamp'] = $vip_end_time; // 时间戳
        $info['now_timestamp'] = time(); // 时间戳
        $info['vip_end_time'] = date("Y-m-d H:i:s", $vip_end_time); //格式化时间
        $info['create_time'] = date("Y-m-d H:i:s", $info['create_time']);
        $order_exist = Db::table('cg_resources_order')->where(['resources_id'=>$params['id'],'user_id'=>$this->user['id'],'status'=>1])->find();
        $info['is_buy'] = 0;
        if(!empty($order_exist['id'])){
            $info['is_buy'] = 1;
        }else{
            $info['phone'] = '***********';
        }

        //商机购买记录
        $info['resourse_order'] = Db::query("select * from cg_resources_order where resources_id = {$params['id']} and status = 1");
        if (!empty($info['resourse_order'])) {
            $list = $info['resourse_order'];
            foreach ($list as $k => $v) {
                $list[$k]['user_name'] = '****';
                if($v['user_id'] == $this->user['id']){
                    $list[$k]['user_name'] = $this->user['nickname'];
                }
                $list[$k]['create_time'] = date('m-d H:i', $v['create_time']);
            }
            $info['resourse_order'] = $list;
        }
        return_success($info);
        //用户注册推送新添加商机的消息
    }
    //我的商机列表
    public function myResourcesList()
    {
        $user = $this->user;
        $list = Db::query("select * from cg_resources_order as t1 left join cg_resources as t2 on (t1.resources_id = t2.id)  where user_id = {$user['id']} and t1.status = 1 order by t1.id desc");
        return_success($list);
    }

}