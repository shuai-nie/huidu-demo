<?php
namespace app\admin\controller;

class Collaborate extends Base
{

    public function _initialize()
    {
        $this->assign('meta_title', '联系提交信息');
        parent::_initialize();
    }

    public function index()
    {
        if(request()->isPost()) {
            $Collaborate = model('Collaborate');
            $page = request()->post('page', 1);
            $limit = request()->post('limit', 10);
            $offset = ($page - 1) * $limit;
            $contact_number = request()->post('contact_number');
            $user_name = request()->post('user_name');
            $source = request()->post('source');
            $map = ['status' => 1];
            if(!empty($contact_number)) {
                $map['contact_number'] = ['like', "%{$contact_number}%"];
            }

            if(!empty($user_name)) {
                $map['user_name'] = ['like', "%{$user_name}%"];
            }

            if(is_numeric($source)){
                $map['source'] = $source;
            }
            $count = $Collaborate->where($map)->count();
            $data = $Collaborate->where($map)->limit($offset, $limit)->order('id desc')->select();

            $dataDic = model('dataDic');
            foreach ($data as $k => $v) {
                $contact_type = $dataDic->where(['data_type_no' => 'CONTACT_TYPE', 'data_no'=>$v['contact_type']])->find();
                if($contact_type){
                    $v['contact_type_name'] = $contact_type['data_name'];
                }
                $data[$k] = $v;
            }

            return json(['data' => ['count' => $count, 'list' => $data]], 200);
        }
        return view('', []);
    }

    public function create()
    {
        $Collaborate = model('Collaborate');
        if(request()->isPost()) {
            $_post = request()->post();
            $state = $Collaborate->save($_post);
            if($state !== false) {
                return success_json('提交成功');
            }
            return error_json('提交成功');
        }
        $type = model('DataDic')->where(['status' => 1, 'data_type_no' => 'CONTACT_TYPE'])->order('sort desc')->select();
        return view('', ['type' => $type]);
    }

    public function edit()
    {
        $Collaborate = model('Collaborate');
        $id = request()->param('id');
        if(request()->isPost()) {
            $_post = request()->post();
            $state = $Collaborate->save($_post, ['id' => $id]);
            if($state !== false) {
                return success_json('提交成功');
            }
            return error_json('提交成功');
        }
        $data = $Collaborate->where(['id'=>$id])->find();
        $type = model('DataDic')->where(['status' => 1, 'data_type_no' => 'CONTACT_TYPE'])->order('sort desc')->select();
        return view('', [
            'data' => $data,
            'type' => $type
        ]);
    }

    public function delete()
    {
        $Collaborate = model('Collaborate');
        $id = request()->param('id');
        $state = $Collaborate->save(['status' => 0], ['id' => $id]);
        if($state !== false) {
            return success_json('提交成功');
        }
        return error_json('提交成功');

    }

    public function quality()
    {
        if(request()->isPost()) {
            $id = request()->post('id');
            $name = request()->post('name');
            $val = request()->post('val');
            $Collaborate = model('Collaborate');
            $state = $Collaborate->save([$name => $val], ['id' => $id]);
            if($state !== false) {
                return success_json('修改成功');
            }
            return error_json('修改成功');
        }
    }

}