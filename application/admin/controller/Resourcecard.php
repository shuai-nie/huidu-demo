<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Db;
use think\Cache;

class Resourcecard extends Base
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
                $map['A.resources_id'] = $resources_id;
            }
            if(!empty($to_uid)) {
                $map['A.to_uid'] = $to_uid;
            }
            if(!empty($from_uid)) {
                $map['A.from_uid'] = $from_uid;
            }
            if(!empty($read_status)) {
                $map['A.read_status'] = $read_status;
            }
            $ResourceCard = model('ResourceCard');
            $User = model('User');
            $resource = model('resource');
            $data = $ResourceCard->alias('A')
                ->join($User->getTable(). ' B', 'A.to_uid=B.id', 'left')
                ->join($User->getTable(). ' C', 'A.from_uid=C.id', 'left')
                ->join($resource->getTable(). ' D', 'A.resources_id=D.id', 'left')
                ->field('A.*,B.username as to_username,C.username as from_username,D.title as resource_title')
                ->where($map)->limit($offset, $limit)->order('id desc')->select();
            $count = $ResourceCard->alias('A')
                ->join($User->getTable(). ' B', 'A.to_uid=B.id', 'left')
                ->join($User->getTable(). ' C', 'A.from_uid=C.id', 'left')
                ->join($resource->getTable(). ' D', 'A.resources_id=D.id', 'left')
                ->where($map)->count();
            return json(['data'=>['count'=>$count, 'list'=>$data]], 200);
        }
        return view();
    }

    public function card_user()
    {
        $uid = \request()->param('uid');
        $card_id = \request()->param('card_id');
        $UserModel = model('User');
        $Card = model('Card');
        $map = [];
        if($uid){
            $map = ['A.uid'=>$uid];
        }
        if($card_id){
            $map = ['A.id'=>$card_id];
        }
        $map['A.status'] = 1;
        $data = $Card->alias('A')
            ->join($UserModel->getTable().' B', 'A.uid=B.id', 'left')
            ->field('A.*,B.username,B.nickname')
            ->where($map)->find();
        if($data){
            $data['business_tag'] = explode('|', $data['business_tag']);
            $CardContact = model('CardContact')->where(['card_id'=>$data['id'],'status'=>1])->select();
            (new Card())->getDataDicTypeNo();
            return view('', [
                'data'=>$data,
                'CardContact' => $CardContact,
            ]);
        } else {
            echo '用户名片不存在';
        }
    }



}
