<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Resource extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        if(Request()->isPost()) {
            $map = ['status'=>1];
            $page = Request()->param('page');
            $limit = Request()->param('limit');
            $offset = ($page - 1) * $limit;
            $data = model("Resource")->where($map)->order('id desc')->limit($offset, $limit)->select();
            $count = model("Resource")->where($map)->count();
            $DataDic = model('DataDic');
            foreach ($data as $key=>$value) {
                $type = explode('|', $value['type']);
                $valueType = [];
                foreach ($type as $val){
                    if (is_numeric($val) ) {
                        $ValuesType = $DataDic->field('id,data_name')->where(['data_type_no'=>'RESOURCES_TYPE','data_no'=>$val])->find();
                        array_push($valueType, !empty($ValuesType) ? $ValuesType['data_name'] : '');
                    }
                }
                $value['type'] = implode('|', $valueType);

                $region = explode('|', $value['region']);
                $valueRegion = [];
                foreach ($region as $val){
                    if (is_numeric($val) ) {
                        $ResourcesType = $DataDic->field('id,data_name')->where(['data_type_no'=>'RESOURCES_REGION','data_no'=>$val])->find();
                        array_push($valueRegion, !empty($ResourcesType) ? $ResourcesType['data_name'] : '');
                    }
                }
                $value['region'] = implode('|', $valueRegion);

                $data[$key] = $value;
            }
            return json(['data'=>['count'=>$count, 'list'=>$data]], 200);
        }
        return view();
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        if(request()->isPost()){
            $_post = request()->param();
            $_post['img'] = isset($_post['img']) ? implode('|', $_post['img']) : '';
            $_post['type'] = isset($_post['type']) ? implode('|', $_post['type']) : '';
            $_post['region'] = isset($_post['region']) ? implode('|', $_post['region']) : '';
            $_post['top_start_time'] = !empty($_post['top_start_time']) ? strtotime($_post['top_start_time']) : 0;
            $_post['top_end_time'] = !empty($_post['top_end_time']) ? strtotime($_post['top_end_time']) : 0;
            $_post['types'] = 2;
            $state = model('Resource')->save($_post);
            if($state !== false){
                return success_json(lang('CreateSuccess', [lang('Resource')]));
            }
            return error_json(lang('CreateFail', [lang('Resource')]));
        }
        $resourcesType = model('DataDic')->where(['data_type_no'=>'RESOURCES_TYPE'])->select();
        $resourcesRegion = model('DataDic')->where(['data_type_no'=>'RESOURCES_REGION'])->select();
        return view('', [
            'resourcesType' => $resourcesType,
            'resourcesRegion' => $resourcesRegion,
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
        if(request()->isPost()){
            $_post = request()->param();
            $_post['img'] = isset($_post['img']) ? implode('|', $_post['img']) : '';
            $_post['type'] = isset($_post['type']) ? implode('|', $_post['type']) : '';
            $_post['region'] = isset($_post['region']) ? implode('|', $_post['region']) : '';
            $_post['top_start_time'] = !empty($_post['top_start_time']) ? strtotime($_post['top_start_time']) : 0;
            $_post['top_end_time'] = !empty($_post['top_end_time']) ? strtotime($_post['top_end_time']) : 0;
            $state = model('Resource')->save($_post, ['id'=>$id]);
            if($state !== false){
                return success_json(lang('EditSuccess', [lang('Resource')]));
            }
            return error_json(lang('EditFail', [lang('Resource')]));
        }
        $Resource = model('Resource');
        $resourceInfo = $Resource->find($id);
        $resourcesType = model('DataDic')->where(['data_type_no'=>'RESOURCES_TYPE'])->select();
        $resourcesRegion = model('DataDic')->where(['data_type_no'=>'RESOURCES_REGION'])->select();
        $resourceInfo['img'] = explode('|', $resourceInfo['img']);
        $resourceInfo['type'] = explode('|', $resourceInfo['type']);
        $resourceInfo['region'] = explode('|', $resourceInfo['region']);
        $resourceInfo['top_start_time'] = $resourceInfo['top_start_time'] > 10000 ? date('Y-m-d H:i:s', $resourceInfo['top_start_time']) : '';
        $resourceInfo['top_end_time'] = $resourceInfo['top_end_time'] > 10000 ? date('Y-m-d H:i:s', $resourceInfo['top_end_time']) : '';
        return view('', [
            'resource'        => $resourceInfo,
            'resourcesType'   => $resourcesType,
            'resourcesRegion' => $resourcesRegion,
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
            $state = model('Resource')->save(['status'=>0], ['id'=>$id]);
            if($state !== false){
                return success_json(lang('DeleteSuccess', [lang('Resource')]));
            }
            return error_json(lang('DeleteFail', [lang('Resource')]));
        }
    }
}
