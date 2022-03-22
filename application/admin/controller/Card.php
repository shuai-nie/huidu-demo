<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Card extends Base
{
    public function _initialize()
    {
        parent::_initialize();
        $this->assign('meta_title', lang('CARD'));
    }

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        if(Request()->isPost()) {
            $map = ['A.status'=>1];
            $page = Request()->post('page');
            $limit = Request()->post('limit');
            $name = \request()->post('name');
            if(!empty($name)) {
                $map['B.username'] = ['like', "%{$name}%"];
            }
            $nickname = \request()->post('nickname');
            if(!empty($nickname)) {
                $map['B.nickname'] = ['like', "%{$nickname}%"];
            }
            $offset = ($page - 1) * $limit;
            $CardModel = model("Card");
            $UserModel = model("User");
            $quality = \request()->post('quality');
            if(is_numeric($quality)) {
                $map['A.quality'] = $quality;
            }
            $data = $CardModel->alias('A')
                ->join($UserModel->getTable().' B', "A.uid=B.id")
                ->field('A.*,B.username,B.nickname')
                ->where($map)->order('A.id desc')->limit($offset, $limit)->select();
            $count = $CardModel->alias('A')
                ->join($UserModel->getTable().' B', "A.uid=B.id")
                ->where($map)->count();
            $dataDic = model('DataDic');

            foreach ($data as $k => $v) {
                $business_tag = explode('|', $v['business_tag']);
                $business_arr = [];
                foreach ($business_tag as $val_tag){
                    if(is_numeric($val_tag)){
                        $dataDicInfo = $dataDic->where(['data_type_no'=>'RESOURCES_TYPE','status'=>1,'data_no'=>$val_tag])->field('data_type_no,data_name')->find();
                        $business_arr[] = $dataDicInfo['data_name'];
                    }
                }
                $v['business_tag'] = implode('|', $business_arr);
                $data[$k] = $v;
            }
            return json(['data'=>['count'=>$count, 'list'=>$data]], 200);
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
        $dataDic = model('DataDic');
        if(\request()->isPost()) {
            $_post = \request()->post();
            $contact = [];
            foreach ($_post['contact'] as $k=>$v) {
                array_push($contact, [
                    'card_id' => $id,
                    'contact_type' => $v,
                    'contact_number' => $_post['tel'][$k]
                ]);
            }
            $_post['business_tag'] = implode('|', $_post['business_tag']);
            $state = model('Card')->save($_post, ['id'=>$id]);
            model('CardContact')->where(['card_id'=>$id])->delete();
            model('CardContact')->saveAll($contact);
            if($state !== false) {
                return success_json(lang('EditSuccess', [lang('CARD')] ));
            }
            return error_json(lang('EditSuccess', [lang('CARD')]) );

        }
        $UserModel = model('User');
        $data = model('Card')->alias('A')
            ->join($UserModel->getTable().' B', 'A.uid=B.id')
            ->field('A.*,B.username,B.nickname')
            ->where(['A.id'=>$id])->find();
        $data['business_tag'] = explode('|', $data['business_tag']);
        $DataDicData = model('DataDic')->where(['data_type_no'=>'CONTACT_TYPE','status'=>1])->order('sort desc')->select();
        $resources = model('DataDic')->where(['data_type_no'=>'RESOURCES_TYPE','status'=>1])->order('sort desc')->select();
        $CardContact = model('CardContact')->where(['card_id'=>$data['id']])->select();
        return view('', [
            'data'=>$data,
            'DataDicData' => $DataDicData,
            'CardContact' => $CardContact,
            'resources' => $resources,
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

    public function quality()
    {
        if(\request()->isPost()){
            $id = \request()->post('id');
            $name = \request()->post('name');
            $val = \request()->post('val');
            $state = model('Card')->save([$name => $val], ['id' => $id]);
            if($state !== false) {
                return success_json(lang('EditSuccess', [lang('CARD')] ));
            }
            return error_json(lang('EditSuccess', [lang('CARD')]) );
        }
    }
}
