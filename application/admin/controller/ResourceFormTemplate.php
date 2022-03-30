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
        if(\request()->isPost()){
            $map = array();
            $limit = \request()->post('limit');
            $page = \request()->post('page');
            $offset = ($page - 1) * $limit;
            $data = $ResourceFormTemplate->alias('A')->where($map)->limit($offset, $limit)->select();
            $count = $ResourceFormTemplate->alias('A')->where($map)->count();
            return json(['data' => ['count' => $count, 'list' => $data]], 200);
        }
        return view('', [
            'meta_title' => '菜单·资源类型',
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
        if(\request()->isPost()){
            $_post = \request()->post();
            $state = $ResourceFormTemplate->save($_post);
            if($state !== false) {
                return success_json(lang('CreateSuccess', [lang('ResourceFormTemplate')]));
            }
            return error_json(lang('CreateFail', [lang('ResourceFormTemplate')]));
        }
        $ty = (new Resource())->ty;
        return view('', [
            'ty' => $ty,
            'form_type' => $this->form_type,
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
        if(\request()->isPost()){
            $_post = \request()->post();
            $state = $ResourceFormTemplate->save($_post, ['id' => $id]);
            if($state !== false) {
                return success_json(lang('EditSuccess', [lang('ResourceFormTemplate')]));
            }
            return error_json(lang('EditFail', [lang('ResourceFormTemplate')]));
        }
        $ty = (new Resource())->ty;
        $ResourceFormTemplateInfo = $ResourceFormTemplate->find($id);
        return view('', [
            'ty' => $ty,
            'form_type' => $this->form_type,
            'ResourceFormTemplateInfo' => $ResourceFormTemplateInfo,
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
