<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;
use think\Cache;

class Resourcecard extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        if(request()->isPost()) {
            $map = array();
            $page = request()->post('page');
            $limit = request()->post('limit');
            $offset = ($page - 1) * $limit;
            $resources_id = request()->post('resources_id');
            $to_uid = request()->post('to_uid');
            $from_uid = request()->post('from_uid');
            $read_status = request()->post('read_status');
            if(!empty($resources_id)) {
                $map['resources_id'] = $resources_id;
            }
            if(!empty($to_uid)) {
                $map['to_uid'] = $to_uid;
            }
            if(!empty($from_uid)) {
                $map['from_uid'] = $from_uid;
            }
            if(!empty($read_status)) {
                $map['read_status'] = $read_status;
            }
            $ResourceCard = model('ResourceCard');
            $data = $ResourceCard->where($map)->limit($offset, $limit)->order('id desc')->select();
            $count = $ResourceCard->where($map)->count();
            foreach ($data as $k => $val) {

                if(is_numeric($val['from_uid'])) {
                    $CacheMember = CacheMember($val['from_uid']);
                    $val['form_username'] = $CacheMember['username'];
                }

                if(is_numeric($val['resources_id'])) {
                    $CacheResource = CacheResource($val['resources_id']);
                    $val['resources_title'] = $CacheResource['title'];
                }

                $data[$k] = $val;
            }
            return json(['data'=>['count'=>$count, 'list'=>$data]], 200);
        }
        return view();
    }



}
