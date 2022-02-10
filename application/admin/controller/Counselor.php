<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Counselor extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        if(request()->isPost()) {
            $map = ['status'=>1];
            $page = request()->param('page');
            $limit = request()->param('limit');
            $name = request()->param('name');
            if(!empty($name)) {
                $map['name'] = array('like', "%{$name}%");
            }
            $offset = ($page - 1) * $limit;
            $Counselor = model('Counselor');
            $data= $Counselor->where($map)->limit($offset, $limit)->select();
            $count = $Counselor->where($map)->count();
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
        if(request()->isPost()) {
            $_post = \request()->param();
            $state = model('Counselor')->save($_post);
            if($state !== false){
                return success_json(lang('CreateSuccess', [lang('Counselor')]) );
            }
            return error_json(lang('CreateFail', [lang('Counselor')]) );
        }
        return view();
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {

    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $Counselor = model('Counselor');
        if(request()->isPost()) {
            $_post = request()->param();
            $state = $Counselor->save($_post, ['id'=>$id]);
            if($state !== false){
                return success_json(lang('EditSuccess', [lang('Counselor')]) );
            }
            return error_json(lang('EditFail', [lang('Counselor')]) );
        }
        $data = $Counselor->find($id);
        return view('', ['data'=>$data]);
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
            $state = model('Counselor')->save(['status'=>0], ['id'=>$id]);
            if($state !== false){
                return success_json(lang('DeleteSuccess', [lang('Counselor')]) );
            }
            return error_json(lang('DeleteFail', [lang('Counselor')]) );
        }
    }
}
