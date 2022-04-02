<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Diction extends Base
{
    public $data = [
        'CONTACT_TYPE'     => ['type' => 'CONTACT_TYPE', 'title' => '联系方式'],
        'RESOURCES_TYPE'   => ['type' => 'RESOURCES_TYPE', 'title' => '资源·合作领域'],
        'RESOURCES_REGION' => ['type' => 'RESOURCES_REGION', 'title' => '资源·合作区域'],
        'RESOURCES_SUBDIVIDE' => ['type' => 'RESOURCES_SUBDIVIDE', 'title' => '资源·业务细分'],
        'REPORT_TYPE' => ['type'=>'REPORT_TYPE', 'title'=>'举报类型'],
        'REPORT_DETAIL_CAUSE' => ['type'=>'REPORT_DETAIL_CAUSE', 'title'=>'举报详细原因'],
        'RESOURCE_INDUSTRY' => ['type'=>'RESOURCE_INDUSTRY', 'title'=>'资源·行业类型'],
        'RESOURCE_INDUSTRY_SUBDIVIDE' => ['type'=>'RESOURCE_INDUSTRY_SUBDIVIDE', 'title'=>'资源·行业细分'],
        'RESOURCE_CURRENCY' => ['type'=>'RESOURCE_CURRENCY', 'title'=>'资源·货币'],

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
                    if(is_numeric($v['data_top_id'])) {
                        $DataInfo = $DataDic->where(['data_type_no'=>'RESOURCES_TYPE', 'data_no'=>$v['data_top_id']])->find();
                        if($DataInfo){
                            $v['data_name'] = '<span class="layui-border-blue layui-btn-xs">' . $DataInfo['data_name'] . '</span>-' . $v['data_name'];
                        }
                    }
                }

                if($v['data_type_no'] == 'REPORT_DETAIL_CAUSE'){
                    if(is_numeric($v['data_top_id'])) {
                        $DataInfo = $DataDic->where(['data_type_no'=>'REPORT_TYPE', 'data_no'=>$v['data_top_id']])->find();
                        if($DataInfo){
                            $v['data_name'] = '<span class="layui-border-blue layui-btn-xs">' . $DataInfo['data_name'] . '</span>-' . $v['data_name'];
                        }
                    }
                }
                if($v['data_type_no'] == 'RESOURCE_INDUSTRY') {
                    if(is_numeric($v['data_top_id'])) {
                        $DataInfo = $DataDic->where(['data_type_no'=>'RESOURCES_SUBDIVIDE', 'id'=>$v['data_top_id']])->find();
                        if($DataInfo){
                            $v['data_name'] = '<span class="layui-border-blue layui-btn-xs">' . $DataInfo['data_name'] . '</span>-' . $v['data_name'];
                        }
                    }
                }
                if($v['data_type_no'] == 'RESOURCE_INDUSTRY_SUBDIVIDE') {
                    if(is_numeric($v['data_top_id'])) {
                        $DataInfo = $DataDic->where(['data_type_no'=>'RESOURCE_INDUSTRY', 'data_no'=>$v['data_top_id']])->find();
                        if($DataInfo){
                            $v['data_name'] = '<span class="layui-border-blue layui-btn-xs">' . $DataInfo['data_name'] . '</span>-' . $v['data_name'];
                        }
                    }
                }

                $data[$k] = $v;
            }
            return json(['data' => ['count' => $count, 'list' => $data]], 200);
        }
        return view('', [
            'data_type' => $this->data,
            'meta_title' => '字典',
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
        $resources = $DataDic->where(['data_type_no'=>'RESOURCES_TYPE','status'=>1])->field('data_type_no,data_top_id,data_no,data_name')->select();
        $reportDetailCauseAll = $DataDic->where(['data_type_no'=>'REPORT_DETAIL_CAUSE','status'=>1])->field('data_type_no,data_top_id,data_no,data_name')->select();
        return view('', [
            'typeData' => $this->data,
            'resources' => $resources,
            'reportDetailCauseAll' => $reportDetailCauseAll
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
        return view('', [
            'data' => $data,
            'typeData' => $this->data,
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

    public function data_top_id()
    {
        $param = \request()->param();
        $DataDic = model('DataDic');
        $data = $DataDic->where(['data_type_no' => $param['data_type'], 'status' => 1])->select();
        return success_json('成功', $data);
    }

}
