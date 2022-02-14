<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Diction extends Controller
{
    public $data = [
        'CONTACT_TYPE'     => ['type' => 'CONTACT_TYPE', 'title' => '联系方式'],
        'RESOURCES_TYPE'   => ['type' => 'RESOURCES_TYPE', 'title' => '资源·业务类型'],
        'RESOURCES_REGION' => ['type' => 'RESOURCES_REGION', 'title' => '资源·合作领域'],
    ];

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
            $data_name = request()->param('data_name');
            $data_type_no = request()->param('data_type_no');
            if (!empty($data_name)) {
                $map['data_name'] = array('like', '%'.$data_name.'%');
            }
            if (!empty($data_type_no)) {
                $map['data_type_no'] = $data_type_no;
            }
            $adAll = model("DataDic")->where($map)->limit($offset, $limit)->order('data_type_no desc,sort desc,id desc')->select();
            $count = model("DataDic")->where($map)->count();
            return json(['data'=>['count'=>$count, 'list'=>$adAll]], 200);
        }
        return view('', ['data_type'=>$this->data]);
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
            $DataDic = model('DataDic');
            $count = $DataDic->where(['data_type_no'=>$_post['data_type_no']])->count();
            $state = $DataDic->save([
                'data_type_no' => $_post['data_type_no'],
                'data_type_name' => $this->data[$_post['data_type_no']]['title'],
                'data_no' => $count,
                'data_name' => $_post['data_name'],
                'data_icon' => $_post['data_icon'],
                'sort' => $_post['sort'],
            ]);
            if($state !== false) {
                return success_json(lang('CreateSuccess', [lang('Dictionaries')] ));
            }
            return error_json(lang('CreateFail', [lang('Dictionaries')]) );
        }
        return view('', ['typeData'=>$this->data]);
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
                'data_type_no'   => $_post['data_type_no'],
                'data_type_name' => $this->data[$_post['data_type_no']]['title'],
                'data_name'      => $_post['data_name'],
                'data_icon'      => $_post['data_icon'],
                'sort'           => $_post['sort'],
            ], ['id' => $id]);
            if($state !== false) {
                return success_json(lang('EditSuccess', [lang('Dictionaries')] ));
            }
            return error_json(lang('EditFail', [lang('Dictionaries')]) );
        }
        $data = $DataDic->find($id);
        return view('', ['data'=>$data,'typeData'=>$this->data]);
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
