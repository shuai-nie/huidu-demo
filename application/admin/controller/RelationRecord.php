<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class RelationRecord extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $DataDic = model('DataDic');
        $RelationRecord = model('RelationRecord');
        if(\request()->isPost()){
            $map = array();
            $page = \request()->post('page');
            $limit = \request()->post('limit');
            $nick_name = \request()->post('nick_name');
            $contact_type = \request()->post('contact_type');
            $ty = \request()->post('ty');
            $resource_type = \request()->post('resource_type');
            $operat = \request()->post('operat');
            if(!empty($nick_name)) {
                $map['nick_name'] = ['like', "%{$nick_name}%"];
            }
            if(is_numeric($contact_type)) {
                $map['contact_type'] = $contact_type;
            }
            if(is_numeric($ty)) {
                $map['ty'] = $ty;
            }
            if(is_numeric($resource_type)) {
                $map['resource_type'] = $resource_type;
            }
            if(!empty($operat)){
                $map['operat'] = $operat;
            }
            $offset = ($page - 1) * $limit;
            $data = $RelationRecord->where($map)->limit($offset, $limit)->order('id desc')->select();
            $count = $RelationRecord->where($map)->count();
            foreach ($data as $key => $value) {
                if(is_numeric( $value['contact_type'])){
                    $contact_type = $DataDic->where(['data_type_no'=>'CONTACT_TYPE', 'status'=>1, 'data_no'=>$value['contact_type']])->find();
                    $value['contact_name'] = $contact_type['data_name'];
                }
                if(is_numeric($value['resource_type'])) {
                    $contact_type = $DataDic->where(['data_type_no'=>'RESOURCES_TYPE', 'status'=>1, 'data_no'=>$value['resource_type']])->find();
                    $value['resource_name'] = $contact_type['data_name'];
                }
                $data[$key] = $value;
            }
            return json(['data' => ['count' => $count, 'list' => $data]], 200);
        }
        $contact = $DataDic->where(['data_type_no'=>'CONTACT_TYPE', 'status'=>1])->field('data_name,data_no')->select();
        $resources = $DataDic->where(['data_type_no'=>'RESOURCES_TYPE', 'status'=>1])->field('data_name,data_no')->select();

        return view('', [
            'meta_title' => '沟通记录表',
            'ty' => (new  Resource())->ty,
            'contact' => $contact,
            'resources' => $resources,
            'operatAll' => $RelationRecord->operat
        ]);
    }

    public function edit($id)
    {
        $RelationRecord = model('RelationRecord');
        if(\request()->isPost()){
            $_post = \request()->post();
            var_dump($_post);
            exit();

        }
        return view('', [
            'operatAll' => $RelationRecord->operat
        ]);
    }



}
