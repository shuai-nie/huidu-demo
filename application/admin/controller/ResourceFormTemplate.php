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
        foreach ($resourcesSubdivide as $k => $v) {
            if(is_numeric($v['data_top_id'])){
                $subdivide = $DataDic->where(['data_type_no' => 'RESOURCES_TYPE', 'data_no' => $v['data_top_id'], 'status'=>1])->count();
                if($subdivide == 0) {
                    unset($resourcesSubdivide[$k]);
                } else {
                    $resourcesSubdivide[$k] = $v;
                }
            }
        }

        return view('', [
            'ty' => $ty,
            'form_type' => $this->form_type,
            'resourcesSubdivide' => $resourcesSubdivide,
        ]);
    }

    public function dd()
    {
        $DataDic = model('DataDic');
        $data = $DataDic->where(['status' => 1, 'data_type_no' => 'RESOURCES_TYPE'])->field('id,data_no,data_name,data_top_id,data_type_name')->select();

        echo "<table cellpadding='1' cellspacing='1'>";
        foreach ($data as $k=> $v) {
            echo "<tr>";
            echo "<td>".$v['data_type_name']."</td>";
            echo "<td>".$v['id'] .'、' .$v['data_name']."Level1</td>";
            echo "</tr>";
            $s = $DataDic->where(['status' => 1, 'data_type_no' => 'RESOURCES_SUBDIVIDE', 'data_top_id'=>$v['data_no']])->field('id,data_no,data_name,data_top_id,data_type_name')->select();
            foreach ($s as $k1 => $v1 ) {
                echo "<tr>";
                echo "<td>".$v1['data_type_name']."</td>";
                echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;".$v1['id'] .'、' .$v1['data_name']."Level2</td>";
                echo "</tr>";
                $s2 = $DataDic->where(['status' => 1, 'data_type_no' => 'RESOURCE_INDUSTRY', 'data_top_id'=>$v1['id']])->field('id,data_no,data_name,data_top_id,data_type_name')->select();
                foreach ($s2 as $k2 => $v2 ) {
                    echo "<tr>";
                    echo "<td>".$v2['data_type_name']."</td>";
                    echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$v2['data_name']."Level3</td>";
                    echo "</tr>";
                }
            }



        }
        echo "</table>";
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
