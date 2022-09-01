<?php
namespace app\admin\controller;

class Firm extends Base
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        if(request()->isPost()){
            $Firm = model('Firm');
            $page = request()->post('page', 1);
            $limit = request()->post('limit');
            $offset = ($page - 1 ) * $limit;

            $map = [];

            $count = $Firm->where($map)->count();
            $data = $Firm->where($map)->order('id desc')->limit($offset, $limit)->select();;
            return json(['data'=>['count'=>$count, 'list'=>$data]], 200);
        }
        return view('', []);
    }

    public function create()
    {
        if(request()->isPost()) {
            $Firm = model('Firm');
            $_post = request()->post();
            $state = $Firm->data($_post)->save();
            if($state !== false) {
                return success_json("提交成功");
            }
            return error_json("提交失败");
        }
        $RESOURCES_TYPE = model('DataDic')->selectType(['data_type_no'=>'RESOURCES_TYPE', 'status'=>1]);
        $RESOURCES_REGION = model('DataDic')->selectType(['data_type_no'=>'RESOURCES_REGION', 'status'=>1]);
        $ADVERT_ATTRIBUTE = model('DataDic')->selectType(['data_type_no'=>'ADVERT_ATTRIBUTE', 'status'=>1]);
        return view('', [
            'RESOURCES_TYPE' => $RESOURCES_TYPE,
            'RESOURCES_REGION' => $RESOURCES_REGION,
            'ADVERT_ATTRIBUTE' => $ADVERT_ATTRIBUTE,
        ]);
    }

    public function edit()
    {
        $id = request()->param('id');
        $Firm = model('Firm');
        if(request()->isPost()) {

            $_post = request()->post();
            $state = $Firm->isUpdate(true)->save($_post, ['id'=>$id]);
            if($state !== false) {
                return success_json("提交成功");
            }
            return error_json("提交失败");
        }
        $info = $Firm->where(['id'=>$id])->find();

        $RESOURCES_TYPE = model('DataDic')->selectType(['data_type_no'=>'RESOURCES_TYPE', 'status'=>1]);
        $RESOURCES_REGION = model('DataDic')->selectType(['data_type_no'=>'RESOURCES_REGION', 'status'=>1]);
        $ADVERT_ATTRIBUTE = model('DataDic')->selectType(['data_type_no'=>'ADVERT_ATTRIBUTE', 'status'=>1]);
        return view('', [
            'info' => $info,
            'RESOURCES_TYPE' => $RESOURCES_TYPE,
            'RESOURCES_REGION' => $RESOURCES_REGION,
            'ADVERT_ATTRIBUTE' => $ADVERT_ATTRIBUTE,
        ]);
    }

    public function delete()
    {
        $id = request()->param('id');
        if(request()->isPost()) {
            $Firm = model('Firm');
            $state = $Firm->isUpdate(true)->save(['status' => 0], ['id' => $id]);
            if($state !== false) {
                return success_json("提交成功");
            }
            return error_json("提交失败");
        }
        return view('', []);
    }


}