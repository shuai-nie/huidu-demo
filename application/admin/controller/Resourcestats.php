<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Resourcestats extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        if(request()->isPost()) {
            $map = array();
            $resources_id = request()->post('resources_id');
            $resources_title = request()->post('resources_title');
            if(!empty($resources_id)) {
                $map['A.resources_id'] = $resources_id;
            }
            if(!empty($resources_title)) {
                $map['B.title'] = ['like', "%{$resources_title}%"];
            }

            $page = request()->param('page');
            $limit = request()->param('limit');
            $offset = ($page - 1) * $limit;
            $ResourceStats = model('ResourceStats');
            $Resource = model('Resource');
            $data = $ResourceStats->alias('A')
                ->join($Resource->getTable().' B', 'A.resources_id=B.id', 'left')
                ->where($map)->field('A.*,B.title as resource_title')->limit($offset, $limit)->select();
            $count = $ResourceStats->alias('A')
                ->join($Resource->getTable().' B', 'A.resources_id=B.id', 'left')
                ->where($map)->count();
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
        //
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
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
