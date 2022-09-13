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

            $map = ['status'=>['in', [1,2,3]]];
            $count = $Firm->where($map)->count();
            $data = $Firm->where($map)->order('id desc')->limit($offset, $limit)->select();

            $DataDic = model('DataDic');
            foreach ($data as $k => $v) {
                $scale = $DataDic->where(['status' => 1, 'data_type_no' => 'FIRM_SCALE', 'data_no'=>$v['scale']])->find();
                if($scale){
                    $v['scale'] = $scale['data_name'];
                }
                //RESOURCES_TYPE
                $business_type = explode('|', $v['business_type']) ;

                $businessTypeAll = $DataDic->where(['status' => 1, 'data_type_no' => 'RESOURCES_TYPE', 'data_no'=>['in', $business_type]])->select();
                $TypeAll = [];
                foreach ($businessTypeAll as $ty){
                    array_push($TypeAll, $ty['data_name']);
                }
                $v['business_type'] = implode('|', $TypeAll);

                $industry = explode('|', $v['industry']);
                $industryAll = $DataDic->where(['status' => 1, 'data_type_no' => 'ADVERT_ATTRIBUTE', 'data_no'=>['in', $industry]])->select();
                $indusAll = [];
                foreach ($industryAll as $try){
                    array_push($indusAll, $try['data_name']);
                }
                $v['industry'] = implode('|', $indusAll);

                $region = explode('|', $v['region']);
                $regionAll = $DataDic->where(['status' => 1, 'data_type_no' => 'RESOURCES_REGION', 'data_no'=>['in', $region]])->select();
                $regAll = [];
                foreach ($regionAll as $reg){
                    array_push($regAll, $reg['data_name']);
                }
                $v['region'] = implode('|', $regAll);

                $data[$k] = $v;
            }
            return json(['data'=>['count'=>$count, 'list'=>$data]], 200);
        }
        return view('', []);
    }

    public function create()
    {
        if(request()->isPost()) {
            $Firm = model('Firm');
            $_post = request()->post();
            if(isset($_post['business_type'])){
                $_post['business_type'] = implode('|', $_post['business_type']);
            }

            if(isset($_post['industry'])){
                $_post['industry'] = implode('|', $_post['industry']);
            }
            if(isset($_post['region'])) {
                $_post['region'] = implode('|', $_post['region']);
            }

            $state = $Firm->data($_post)->save();
            if($state !== false) {
                return success_json("提交成功");
            }
            return error_json("提交失败");
        }
        $RESOURCES_TYPE = model('DataDic')->selectType(['data_type_no'=>'RESOURCES_TYPE', 'status'=>1]);
        $RESOURCES_REGION = model('DataDic')->selectType(['data_type_no'=>'RESOURCES_REGION', 'status'=>1]);
        $ADVERT_ATTRIBUTE = model('DataDic')->selectType(['data_type_no'=>'ADVERT_ATTRIBUTE', 'status'=>1]);
        $FIRM_SCALE = model('DataDic')->selectType(['data_type_no'=>'FIRM_SCALE', 'status'=>1]);
        return view('', [
            'RESOURCES_TYPE' => $RESOURCES_TYPE,
            'RESOURCES_REGION' => $RESOURCES_REGION,
            'ADVERT_ATTRIBUTE' => $ADVERT_ATTRIBUTE,
            'FIRM_SCALE' => $FIRM_SCALE,
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
        $info['business_type'] = explode('|', $info['business_type']);
        $info['industry'] = explode('|', $info['industry']);
        $info['region'] = explode('|', $info['region']);

        $RESOURCES_TYPE = model('DataDic')->selectType(['data_type_no'=>'RESOURCES_TYPE', 'status'=>1]);
        $RESOURCES_REGION = model('DataDic')->selectType(['data_type_no'=>'RESOURCES_REGION', 'status'=>1]);
        $ADVERT_ATTRIBUTE = model('DataDic')->selectType(['data_type_no'=>'ADVERT_ATTRIBUTE', 'status'=>1]);
        $FIRM_SCALE = model('DataDic')->selectType(['data_type_no'=>'FIRM_SCALE', 'status'=>1]);
        return view('', [
            'info' => $info,
            'RESOURCES_TYPE' => $RESOURCES_TYPE,
            'RESOURCES_REGION' => $RESOURCES_REGION,
            'ADVERT_ATTRIBUTE' => $ADVERT_ATTRIBUTE,
            'FIRM_SCALE' => $FIRM_SCALE,
        ]);
    }

    public function delete()
    {
        $id = request()->param('id');

            $Firm = model('Firm');
            $state = $Firm->isUpdate(true)->save(['status' => 0], ['id' => $id]);
            if($state !== false) {
                return success_json("提交成功");
            }
            return error_json("提交失败");

    }


}