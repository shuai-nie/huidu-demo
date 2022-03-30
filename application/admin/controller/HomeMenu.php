<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class HomeMenu extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $HomeMenu =model('HomeMenu');
        $DataDic =model('DataDic');
        if(\request()->isPost()){
            $map = ['A.status'=>1];
            $limit = \request()->post('limit');
            $page = \request()->post('page', 1);
            $data_name = \request()->post('data_name');
            if(!empty($data_name)) {
                $map['B.data_name'] = ['like', "%{$data_name}%"];
            }
            $offset = ($page - 1) * $limit;
            $data = $HomeMenu->alias('A')
                ->join($DataDic->getTable().' B', 'A.business_type=B.data_no and B.data_type_no="RESOURCES_TYPE"', 'left')
                ->field('A.*,B.data_name')
                ->where($map)->limit($offset, $limit)->order('A.id desc')->select();
            $count = $HomeMenu->alias('A')
                ->join($DataDic->getTable().' B', 'A.business_type=B.data_no and B.data_type_no="RESOURCES_TYPE"', 'left')
                ->where($map)->count();
            return json(['data' => ['count' => $count, 'list' => $data]], 200);
        }
        return view('', [
                'meta_title' => '菜单·资源类型'
            ]);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        $HomeMenu = model('HomeMenu');
        if(\request()->isPost()){
            $data = \request()->post('');
            $state = $HomeMenu->save($data);
            if($state !== false) {
                return success_json(lang('CreateSuccess', [lang('HomeMenu')]));
            }
            return error_json(lang('CreateFail', [lang('HomeMenu')]));
        }
        $ResourceType = model('DataDic')->where(['data_type_no'=>'RESOURCES_TYPE', 'status'=>1])->field('id,data_name,data_top_id,data_no')->select();
        return view('', ['ResourceType'=>$ResourceType]);
    }


    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $HomeMenu = model('HomeMenu');
        if(\request()->isPost()){
            $_post = \request()->post('');
            $state = $HomeMenu->save($_post, ['id'=>$id]);
            if($state !== false) {
                return success_json(lang('EditSuccess', [lang('HomeMenu')]));
            }
            return error_json(lang('EditFail', [lang('HomeMenu')]));
        }
        $ResourceType = model('DataDic')->where(['data_type_no'=>'RESOURCES_TYPE', 'status'=>1])->field('id,data_name,data_top_id,data_no')->select();
        $HomeMenuInfo = $HomeMenu->find($id);
        return view('', [
            'ResourceType' => $ResourceType,
            'HomeMenuInfo' => $HomeMenuInfo,
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
        $HomeMenu = model('HomeMenu');
        if($id > 0){
            $state = $HomeMenu->save(['status' => 0], ['id' => $id]);
            if($state !== false) {
                return success_json(lang('DeleteSuccess', [lang('HomeMenu')]));
            }
            return error_json(lang('DeleteFail', [lang('HomeMenu')]));
        }
    }
}
