<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Complaint extends Base
{
    /**
     * 显示资源列表
     * @return \think\Response
     */
    public function index()
    {

        if(\request()->isPost()) {
            $page = \request()->post('page');
            $limit = \request()->post('limit');
            $Complaint = model('Complaint');
            $DataDic = model('DataDic');
            $offset = ($page - 1 ) * $limit;
            $map = [] ;
            $data = $Complaint->where($map)->limit($offset, $limit)->order('id desc')->select();
            foreach ($data as $k => $v){
                $detail_cause = json_decode($v['detail_cause'], true);
                $detail_cause = implode(',', $detail_cause);
                $v['detail_cause'] = $detail_cause;

                $DataDicFind = $DataDic->where(['status'=>1,'data_type_no'=>'REPORT_TYPE','data_no'=>$v['report_type']])->field('data_no,data_name')->find();
                $v['report_type'] = $DataDicFind['data_name'];
                $UserFind = CacheUser($v['uid']);
                $v['username'] = $UserFind['username'];
                $data[$k] = $v;
            }
            $count = $Complaint->where($map)->count();
            return json(['data'=>['count'=>$count, 'list'=>$data]], 200);
        }
        return view('', ['meta_title'=>'资源投诉处理']);
    }

    /**
     * 显示编辑资源表单页.
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $Complaint = model('Complaint');
        if(\request()->isPost()){
            $disposeResult = \request()->post('dispose_result');
            $state = $Complaint->save(['dispose_result' => $disposeResult], ['id' => $id]);
            if($state !== false){
                return success_json('提交成功');
            }
            return error_json('提交失败');
        }
        $data = $Complaint->find($id);
        if($data['detail_cause']){
            $detail_cause = json_decode($data['detail_cause'], true);
            $detail_cause = implode(',', $detail_cause);
            $data['detail_cause'] = $detail_cause;
        }
        $userAll = model('User')->where(['status'=>1])->field('id,username')->select();
        $DataDicAll = model('DataDic')->where(['status'=>1,'data_type_no'=>'REPORT_TYPE'])->field('data_no,data_name')->select();
        return view('', ['data' => $data, 'user' => $userAll, 'DataDicAll' => $DataDicAll]);
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
