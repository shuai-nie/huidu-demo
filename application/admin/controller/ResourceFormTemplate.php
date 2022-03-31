<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class ResourceFormTemplate extends Controller
{
    public $form_type = [
        0 => '短文本',
        1 => '长文本',
        2 => '数字文本',
        3 => '百分比',
        4 => '时间选择器',
        5 => '价格',
        6 => '富文本',
        7 => '产品图片',
        8 => 'logo',
    ];
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $ResourceFormTemplate = model('ResourceFormTemplate');
        $DataDic = model('DataDic');
        if(\request()->isPost()){
            $map = array('A.status'=>1);
            $limit = \request()->post('limit');
            $page = \request()->post('page');
            $fill_flag = \request()->post('fill_flag');
            $form_type = \request()->post('form_type');
            $ty = \request()->post('ty');
            $business_subdivide = \request()->post('business_subdivide');
            $form_title = \request()->post('form_title');
            $offset = ($page - 1) * $limit;
            if(is_numeric($fill_flag)){
                $map['A.fill_flag'] = $fill_flag;
            }
            if(is_numeric($form_type)){
                $map['A.form_type'] = $form_type;
            }
            if(is_numeric($ty)){
                $map['A.ty'] = $ty;
            }
            if(is_numeric($business_subdivide)){
                $map['A.business_subdivide'] = $business_subdivide;
            }
            if(!empty($form_title)){
                $map['A.form_title'] = ['like', "%{$form_title}%"];
            }
            $data = $ResourceFormTemplate->alias('A')
                ->join($DataDic->getTable().' B', '(A.type=B.data_no and B.data_type_no="RESOURCES_TYPE")', 'left')
                ->join($DataDic->getTable().' C', '(A.business_subdivide=C.data_no and C.data_type_no="RESOURCES_SUBDIVIDE")', 'left')
                ->field('A.*,B.data_type_name,B.data_name AS type_name,C.data_name AS business_subdivide_name')
                ->where($map)->limit($offset, $limit)->order('id desc')->select();
            $count = $ResourceFormTemplate->alias('A')
                ->join($DataDic->getTable().' B', '(A.type=B.data_no and B.data_type_no="RESOURCES_TYPE")', 'left')
                ->join($DataDic->getTable().' C', '(A.business_subdivide=C.data_no and C.data_type_no="RESOURCES_SUBDIVIDE")', 'left')
                ->where($map)->count();
            return json(['data' => ['count' => $count, 'list' => $data]], 200);
        }
        $resourcesSubdivide = $DataDic->where(['data_type_no'=>'RESOURCES_SUBDIVIDE', 'status'=>1])->select();
        return view('', [
            'meta_title' => '资源·表单模板',
            'form_type' => $this->form_type,
            'ty' => (new Resource())->ty,
            'resourcesSubdivide' => $resourcesSubdivide,
        ]);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        $ResourceFormTemplate = model('ResourceFormTemplate');
        $DataDic = model('DataDic');
        if(\request()->isPost()){
            $_post = \request()->post();
            $DataDicInfo = $DataDic->where(['data_type_no'=>'RESOURCES_SUBDIVIDE','status'=>1, 'data_no'=>$_post['business_subdivide']])->find();
            $_post['type'] = $DataDicInfo['data_top_id'];
            $state = $ResourceFormTemplate->save($_post);
            if($state !== false) {
                return success_json(lang('CreateSuccess', [lang('ResourceFormTemplate')]));
            }
            return error_json(lang('CreateFail', [lang('ResourceFormTemplate')]));
        }
        $ty = (new Resource())->ty;
        $resourcesSubdivide = $DataDic->where(['data_type_no'=>'RESOURCES_SUBDIVIDE', 'status'=>1])->select();
        return view('', [
            'ty' => $ty,
            'form_type' => $this->form_type,
            'resourcesSubdivide' => $resourcesSubdivide,
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
        $ResourceFormTemplate = model('ResourceFormTemplate');
        $DataDic = model('DataDic');
        if(\request()->isPost()){
            $_post = \request()->post();
            $DataDicInfo = $DataDic->where(['data_type_no'=>'RESOURCES_SUBDIVIDE','status'=>1, 'data_no'=>$_post['business_subdivide']])->find();
            $_post['type'] = $DataDicInfo['data_top_id'];
            $state = $ResourceFormTemplate->save($_post, ['id' => $id]);
            if($state !== false) {
                return success_json(lang('EditSuccess', [lang('ResourceFormTemplate')]));
            }
            return error_json(lang('EditFail', [lang('ResourceFormTemplate')]));
        }
        $ty = (new Resource())->ty;
        $ResourceFormTemplateInfo = $ResourceFormTemplate->find($id);
        $resourcesSubdivide = $DataDic->where(['data_type_no'=>'RESOURCES_SUBDIVIDE', 'status'=>1])->select();
        return view('', [
            'ty' => $ty,
            'form_type' => $this->form_type,
            'ResourceFormTemplateInfo' => $ResourceFormTemplateInfo,
            'resourcesSubdivide' => $resourcesSubdivide,
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
        $ResourceFormTemplate = model('ResourceFormTemplate');
        if($id > 0){
            $state = $ResourceFormTemplate->save(['status' => 0], ['id' => $id]);
            if($state !== false) {
                return success_json(lang('DeleteSuccess', [lang('ResourceFormTemplate')]));
            }
            return error_json(lang('DeleteFail', [lang('ResourceFormTemplate')]));
        }
    }
}
