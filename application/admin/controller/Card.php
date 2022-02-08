<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Card extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        if(Request()->isPost()) {
            $map = ['A.status'=>1];
            $page = Request()->param('page');
            $limit = Request()->param('limit');
            $offset = ($page - 1) * $limit;
            $CardAll = model("Card")->alias('A')
                ->join(model('CardContact')->getTable().' B', 'A.id=B.card_id', 'LEFT')
                ->field('A.*,B.contact_type,B.contact_number')
                ->where($map)->limit($offset, $limit)->select();
            $count = model("Card")->alias('A')
                ->join(model('CardContact')->getTable().' B', 'A.id=B.card_id', 'LEFT')
                ->where($map)->count();
            return json(['data'=>['count'=>$count, 'list'=>$CardAll]], 200);
        }
        return view('');
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        if(Request()->isPost()) {
            $data = Request()->param();
            $data['create_id'] = getLoginUserId();
            $data['update_id'] = getLoginUserId();
            $state = model("Card")->save($data);
            if($state !== false){
                return success_json();
            }
            return error_json();
        }
        return view();
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
