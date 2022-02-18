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
            $resources_id = request()->param('resources_id');
            $resources_title = request()->param('resources_title');
            if(!empty($resources_id)) {
                $map['resources_id'] = $resources_id;
            }

            $page = request()->param('page');
            $limit = request()->param('limit');
            $offset = ($page - 1) * $limit;
            $ResourceStats = model('ResourceStats');
            $data = $ResourceStats->where($map)->limit($offset, $limit)->select();
            $count = $ResourceStats->where($map)->count();
            foreach ($data as $k => $val) {
                if(is_numeric($val['resources_id'])) {
                    $CacheResource = CacheResource($val['resources_id']);
                    $val['resources_title'] = $CacheResource['title'];
                }
                $data[$k] = $val;
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
