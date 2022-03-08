<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Diction extends Base
{
    public $data = [
        'CONTACT_TYPE'     => ['type' => 'CONTACT_TYPE', 'title' => '联系方式'],
        'RESOURCES_TYPE'   => ['type' => 'RESOURCES_TYPE', 'title' => '资源·业务类型'],
        'RESOURCES_REGION' => ['type' => 'RESOURCES_REGION', 'title' => '资源·合作领域'],
        'RESOURCES_SUBDIVIDE' => ['type' => 'RESOURCES_SUBDIVIDE', 'title' => '资源·业务细分'],
    ];

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $DataDic = model("DataDic");
        if(Request()->isPost()) {
            $map = ['status'=>1];
            $page = Request()->post('page');
            $limit = Request()->post('limit');
            $offset = ($page - 1) * $limit;
            $data_name = request()->post('data_name');
            $data_type_no = request()->post('data_type_no');
            if (!empty($data_name)) {
                $map['data_name'] = array('like', '%'.$data_name.'%');
            }
            if (!empty($data_type_no)) {
                $map['data_type_no'] = $data_type_no;
            }
            $data = $DataDic->where($map)->limit($offset, $limit)->order('data_type_no desc,sort desc,id desc')->select();
            $count = $DataDic->where($map)->count();
            foreach ($data as $k => $v) {
                if($v['data_type_no'] == 'RESOURCES_SUBDIVIDE'){
                    if($v['data_top_id'] > 0) {
                        $DataInfo = $DataDic->where(['data_type_no'=>'RESOURCES_SUBDIVIDE', 'data_no'=>$v['data_top_id']])->find();
                        if($DataInfo){
                            $v['data_name'] = '<span class="layui-btn layui-btn-primary layui-border-blue layui-btn-xs">' . $DataInfo['data_name'] . '</span>-' . $v['data_name'];
                        }
                    } else {
                        $v['data_name'] = '<span class="layui-btn layui-btn-primary layui-border-blue layui-btn-xs">顶级</span>-' . $v['data_name'];
                    }
                }
                $data[$k] = $v;
            }
            return json(['data' => ['count' => $count, 'list' => $data]], 200);
        }
        return view('', [
            'data_type' => $this->data,
        ]);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        $DataDic = model('DataDic');
        if(request()->isPost()){
            $_post = request()->post();
            $Datafind = $DataDic->where(['data_type_no'=>$_post['data_type_no']])->order('data_no desc')->find();
            $state = $DataDic->save([
                'data_type_no' => $_post['data_type_no'],
                'data_type_name' => $this->data[$_post['data_type_no']]['title'],
                'data_no' => $Datafind['data_no']+1,
                'data_name' => $_post['data_name'],
                'data_icon' => $_post['data_icon'],
                'sort' => $_post['sort'],
                'data_top_id' => $_post['data_top_id'],
            ]);
            if($state !== false) {
                return success_json(lang('CreateSuccess', [lang('Dictionaries')] ));
            }
            return error_json(lang('CreateFail', [lang('Dictionaries')]) );
        }
        $subdivide = $DataDic->where(['data_type_no'=>'RESOURCES_SUBDIVIDE', 'data_top_id'=>0, 'status'=>1])->field('data_type_no,data_top_id,data_no,data_name')->select();
        return view('', [
            'typeData' => $this->data,
            'subdivide' => $subdivide,
        ]);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $DataDic = model('DataDic');
        if(request()->isPost()) {
            $_post   = request()->param();
            $state = $DataDic->save([
                'data_type_no' => $_post['data_type_no'],
                'data_type_name' => $this->data[$_post['data_type_no']]['title'],
                'data_name' => $_post['data_name'],
                'data_icon' => $_post['data_icon'],
                'sort' => $_post['sort'],
                'data_top_id' => $_post['data_top_id'],
            ], ['id' => $id]);
            if($state !== false) {
                return success_json(lang('EditSuccess', [lang('Dictionaries')] ));
            }
            return error_json(lang('EditFail', [lang('Dictionaries')]) );
        }
        $data = $DataDic->find($id);
        $subdivide = $DataDic->where(['data_type_no'=>'RESOURCES_SUBDIVIDE', 'data_top_id'=>0, 'status'=>1])->field('data_type_no,data_top_id,data_no,data_name')->select();
        return view('', [
            'data' => $data,
            'typeData' => $this->data,
            'subdivide' => $subdivide,
        ]);
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        if($id != '') {
            $state = model('DataDic')->save(['status'=>0], ['id'=>$id]);
            if($state !== false) {
                return success_json(lang('DeleteSuccess', [lang('Dictionaries')] ));
            }
            return error_json(lang('DeleteFail', [lang('Dictionaries')]) );
        }
    }

}
