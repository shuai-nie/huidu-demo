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
            $map = ['status'=>1];
            $page = Request()->param('page');
            $limit = Request()->param('limit');
            $offset = ($page - 1) * $limit;
            $CardAll = model("Card")->where($map)->limit($offset, $limit)->select();
            $count = model("Card")->where($map)->count();
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
            $state = true;
            if($state !== false){
                return success_json();
            }
            return error_json();
        }
        return view();
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        if(\request()->isPost()) {
            $_post = \request()->param();
            $contact = [];
            foreach ($_post['contact'] as $k=>$v) {
                array_push($contact, [
                    'card_id' => $id,
                    'contact_type' => $v,
                    'contact_number' => $_post['tel'][$k]
                ]);
            }
            $state = model('Card')->save($_post, ['id'=>$id]);
            model('CardContact')->where(['card_id'=>$id])->delete();
            model('CardContact')->saveAll($contact);
            if($state !== false) {
                return success_json(lang('EditSuccess', [lang('CARD')] ));
            }
            return error_json(lang('EditSuccess', [lang('CARD')]) );

        }
        $data = model('Card')->find($id);
        $DataDicData = model('DataDic')->where(['data_type_no'=>'CONTACT_TYPE','status'=>1])->order('sort desc')->select();
        $CardContact = model('CardContact')->where(['card_id'=>$data['id']])->select();
        return view('', [
            'data'=>$data,
            'DataDicData' => $DataDicData,
            'CardContact' => $CardContact,
        ]);
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
