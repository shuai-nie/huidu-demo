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
            $offset = ($page - 1 ) * $limit;
            $map = [] ;
            $data = $Complaint->where($map)->limit($offset, $limit)->order('id desc')->select();
            $count = $Complaint->where($map)->count();
            return json(['data'=>['count'=>$count, 'list'=>$data]], 200);
        }
        return view('', ['meta_title'=>'资源投诉处理']);
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
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
