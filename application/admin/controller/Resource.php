<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Resource extends Base
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
            $page = Request()->post('page');
            $limit = Request()->post('limit');
            $offset = ($page - 1) * $limit;
            $uid = \request()->post('uid');
            if(!empty($uid)) {
                $map['uid'] = $uid;
            }
            $title = \request()->post('title');
            if(!empty($title)) {
                $map['title'] = ['like', "%{$title}%"];
            }
            $auth = \request()->post('auth');
            if(!empty($auth)) {
                $map['auth'] = $auth;
            }
            $ty = \request()->post('ty');
            if(!empty($ty)) {
                $map['ty'] = $ty;
            }
            $data = model("Resource")->where($map)->order('id desc')->limit($offset, $limit)->select();
            $count = model("Resource")->where($map)->count();
            $DataDic = model('DataDic');
            foreach ($data as $key=>$value) {
                $type = explode('|', $value['type']);
                $valueType = [];
                foreach ($type as $val){
                    if (is_numeric($val) ) {
                        $ValuesType = $DataDic->field('id,data_name')->where(['data_type_no'=>'RESOURCES_TYPE','data_no'=>$val])->find();
                        array_push($valueType, !empty($ValuesType) ? $ValuesType['data_name'] : '');
                    }
                }
                $value['type'] = implode('|', $valueType);

                $region = explode('|', $value['region']);
                $valueRegion = [];
                foreach ($region as $val){
                    if (is_numeric($val) ) {
                        $ResourcesType = $DataDic->field('id,data_name')->where(['data_type_no'=>'RESOURCES_REGION','data_no'=>$val])->find();
                        array_push($valueRegion, !empty($ResourcesType) ? $ResourcesType['data_name'] : '');
                    }
                }
                $value['region'] = implode('|', $valueRegion);

                $data[$key] = $value;
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
        if(request()->isPost()){
            $_post = request()->post();
            $contact = [];
            $_post['img'] = isset($_post['img']) ? implode('|', $_post['img']) : '';
            $_post['type'] = isset($_post['type']) ? implode('|', $_post['type']) : '';
            $_post['region'] = isset($_post['region']) ? implode('|', $_post['region']) : '';
            $_post['top_start_time'] = !empty($_post['top_start_time']) ? strtotime($_post['top_start_time']) : 0;
            $_post['top_end_time'] = !empty($_post['top_end_time']) ? strtotime($_post['top_end_time']) : 0;
            $_post['types'] = 2;
            $_post['flush_time'] = time();
            if($_post['auth'] == 1) {
                $this->userpublish($_post['uid']);
            }

            $state = model('Resource')->save($_post);

            $resources_id = model('Resource')->getLastInsID();
            foreach ($_post['contactName'] as $k=>$v){
                foreach ($_post['contact'][$k] as $k1=>$v1) {
                    if($resources_id && $v && $_post['contact'][$k][$k1] && $_post['tel'][$k][$k1] ) {
                    array_push($contact, [
                        'resources_id' => $resources_id,
                        'name' => $v,
                        'type'   => $_post['contact'][$k][$k1],
                        'number' => $_post['tel'][$k][$k1]
                    ]);
                    }
                }
            }

            if($contact){
                $state1 = model('ResourceContact')->saveAll($contact);
            }
            if($state !== false){
                $userInfo = model('UserInfo')->where(['uid'=>$_post['uid']])->find();
                model('UserRecharge')->where(['id'=>$userInfo['user_recharge_id']])->setInc('used_publish');
                return success_json(lang('CreateSuccess', [lang('Resource')]));
            }
            return error_json(lang('CreateFail', [lang('Resource')]));
        }
        $resourcesType = model('DataDic')->where(['data_type_no'=>'RESOURCES_TYPE'])->select();
        $resourcesRegion = model('DataDic')->where(['data_type_no'=>'RESOURCES_REGION'])->select();
        $DataDicData = model('DataDic')->where(['data_type_no'=>'CONTACT_TYPE','status'=>1])->order('sort desc')->select();
        return view('', [
            'resourcesType' => $resourcesType,
            'resourcesRegion' => $resourcesRegion,
            'DataDicData' => $DataDicData,
        ]);
    }

    private function userpublish($uid)
    {
        $UserRecharge = model('UserRecharge');
        $UserCount = $UserRecharge->alias('A')->join(model('UserInfo')->getTable() . " B", "A.id=B.user_recharge_id")
            ->where(['B.uid'=>$uid])->field("A.publish,A.used_publish")->find();

        if($UserCount['publish'] <= $UserCount['used_publish']) {
            echo json_encode([
                'msg' => '用户发布次数已用完',
                'code' => 400,
            ], JSON_UNESCAPED_UNICODE);exit();
        }
        return true;
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $Resource = model('Resource');
        $resourceInfo = $Resource->find($id);
        if(request()->isPost()){
            $_post = request()->post();

            $_post['img'] = isset($_post['img']) ? implode('|', $_post['img']) : '';
            $_post['type'] = isset($_post['type']) ? implode('|', $_post['type']) : '';
            $_post['region'] = isset($_post['region']) ? implode('|', $_post['region']) : '';
            $_post['top_start_time'] = !empty($_post['top_start_time']) ? strtotime($_post['top_start_time']) : 0;
            $_post['top_end_time'] = !empty($_post['top_end_time']) ? strtotime($_post['top_end_time']) : 0;

            if($_post['auth'] == 1){
                $this->userpublish($_post['uid']);
            }

            $state = $Resource->save($_post, ['id'=>$id]);
            $contact = [];
            foreach ($_post['contactName'] as $k=>$v){
                foreach ($_post['contact'][$k] as $k1=>$v1) {
                    if($id && $v && $_post['contact'][$k][$k1] && $_post['tel'][$k][$k1] ) {
                    array_push($contact, [
                        'resources_id' => $id,
                        'name' => $v,
                        'type'   => $_post['contact'][$k][$k1],
                        'number' => $_post['tel'][$k][$k1]
                    ]);
                    }
                }
            }
            if($contact){
                model('ResourceContact')->where(['resources_id'=>$id])->delete();
                $state1 = model('ResourceContact')->saveAll($contact);
            }
            if($state !== false){
                if($resourceInfo['auth'] != $_post['auth'] && $_post['auth'] == 1 ){
                    $userInfo = model('UserInfo')->where(['uid'=>$_post['uid']])->find();
                    model('UserRecharge')->where(['id'=>$userInfo['user_recharge_id']])->setInc('used_publish');
                }

                return success_json(lang('EditSuccess', [lang('Resource')]));
            }
            return error_json(lang('EditFail', [lang('Resource')]));
        }

        $resourcesType = model('DataDic')->where(['data_type_no'=>'RESOURCES_TYPE'])->select();
        $resourcesRegion = model('DataDic')->where(['data_type_no'=>'RESOURCES_REGION'])->select();
        $resourceInfo['img'] = explode('|', $resourceInfo['img']);
        $resourceInfo['type'] = explode('|', $resourceInfo['type']);
        $resourceInfo['region'] = explode('|', $resourceInfo['region']);
        $resourceInfo['top_start_time'] = $resourceInfo['top_start_time'] > 10000 ? date('Y-m-d H:i:s', $resourceInfo['top_start_time']) : '';
        $resourceInfo['top_end_time'] = $resourceInfo['top_end_time'] > 10000 ? date('Y-m-d H:i:s', $resourceInfo['top_end_time']) : '';

        $DataDicData = model('DataDic')->where(['data_type_no'=>'CONTACT_TYPE','status'=>1])->order('sort desc')->select();
        $ResourceContact = model('ResourceContact')->where(['resources_id'=>$resourceInfo->id,'status'=>1])->select();
        if($ResourceContact) {
            $ResourceContact = collection($ResourceContact)->toArray();
        }
        $ResourceContact = \util\Tree::array_group_by($ResourceContact, 'name');
        return view('', [
            'resource'        => $resourceInfo,
            'resourcesType'   => $resourcesType,
            'resourcesRegion' => $resourcesRegion,
            'DataDicData' => $DataDicData,
            'ResourceContact' => $ResourceContact,
        ]);
    }

    protected function res($data)
    {

    }

    public function topping($id)
    {
        $Resource = model('Resource');
        if(\request()->isPost()) {
            $_post = \request()->post();
            $_post['top_start_time'] = !empty($_post['top_start_time']) ? strtotime($_post['top_start_time']) : '';
            $_post['top_end_time'] = !empty($_post['top_end_time']) ? strtotime($_post['top_end_time']) : '';
            $state = $Resource->save($_post, ['id'=>$id]);
            if($state !== false){
                return success_json(lang('EditSuccess', [lang('Resource')]));
            }
            return error_json(lang('EditFail', [lang('Resource')]));
        }

        $data = $Resource->find($id);
        $data['top_start_time'] = $data['top_start_time'] > 10000 ? date('Y-m-d H:i:s', $data['top_start_time']) : '';
        $data['top_end_time'] = $data['top_end_time'] > 10000 ? date('Y-m-d H:i:s', $data['top_end_time']) : '';
        return view('', ['data'=>$data]);
    }

    /**
     * 删除指定资源
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        if($id != '') {
            $state = model('Resource')->save(['status'=>0], ['id'=>$id]);
            if($state !== false){
                return success_json(lang('DeleteSuccess', [lang('Resource')]));
            }
            return error_json(lang('DeleteFail', [lang('Resource')]));
        }
    }

    public function toplist()
    {
        if(Request()->isPost()) {
            $map = ['status'=>1,'auth'=>1];
            $page = Request()->post('page');
            $limit = Request()->post('limit');
            $offset = ($page - 1) * $limit;
            $uid = \request()->post('uid');
            if(!empty($uid)) {
                $map['uid'] = $uid;
            }
            $title = \request()->post('title');
            if(!empty($title)) {
                $map['title'] = ['like', "%{$title}%"];
            }
            $auth = \request()->post('auth');
            if(!empty($auth)) {
                $map['auth'] = $auth;
            }
            $ty = \request()->post('ty');
            if(!empty($ty)) {
                $map['ty'] = $ty;
            }
            $data = model("Resource")->where($map)->order('top_end_time desc,id desc')->limit($offset, $limit)->select();
            $count = model("Resource")->where($map)->count();
            return json(['data'=>['count'=>$count, 'list'=>$data]], 200);
        }
        return view();
    }
}
