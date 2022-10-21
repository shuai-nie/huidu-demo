<?php
namespace app\admin\controller;

class Firm extends Base
{
    public function _initialize()
    {
        $this->assign('meta_title', '企业审核列表');
        parent::_initialize();
    }

    public function index()
    {
        $Firm = model('Firm');
        if(request()->isPost()){
            $DataDic = model('DataDic');
            $Card = model('Card');
            $page = request()->post('page', 1);
            $limit = request()->post('limit');
            $offset = ($page - 1 ) * $limit;
            $name = request()->post('name');
            $status = request()->post('status');
            $isweb = request()->post('isweb');

            $map = ['status'=>['in', [1,2,3]]];
            if(!empty($name)) {
                $map['name'] = ['like', '%'.$name.'%'];
            }
            if(!empty($status)) {
                $map['status'] = $status;
            }
            if(!empty($isweb)) {
                $map['isweb'] = $isweb;
            }

            $count = $Firm->where($map)->count();
            $exp = new \think\Db\Expression('field(status, 1,2,3), id desc');
            $data = $Firm->where($map)->order($exp)->limit($offset, $limit)->select();


            foreach ($data as $k => $v) {
                $scale = $DataDic->where(['status' => 1, 'data_type_no' => 'FIRM_SCALE', 'data_no'=>$v['scale']])->find();
                if($scale){
                    $v['scale'] = $scale['data_name'];
                }

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

                $v['userZ'] = $Card->where(['firm_id'=>$v['id'], 'status'=>1])->count();
                $v['userC'] = $Card->where(['firm_id'=>$v['id'], 'status'=>1, 'verify_status'=>1])->count();

                $data[$k] = $v;
            }
            return json(['data'=>['count'=>$count, 'list'=>$data]], 200);
        }
        getAdminLog("查看 企业审核列表");
        return view('', [
            'web_type' => $Firm->web_type
        ]);
    }

    public function create()
    {
        if(request()->isPost()) {
            $Firm = model('Firm');
            $_post = request()->post();

            $nameCount = $Firm->where(['name' => $_post['name']])->count();
            if($nameCount > 0 ){
                return error_json('企业名称重复请修改');
            }

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
                getAdminLog(" 新建 企业审核列表 ". $Firm->id);
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

            if(isset($_post['business_type'])){
                $_post['business_type'] = implode('|', $_post['business_type']);
            }

            if(isset($_post['industry'])){
                $_post['industry'] = implode('|', $_post['industry']);
            }
            if(isset($_post['region'])) {
                $_post['region'] = implode('|', $_post['region']);
            }

            $state = $Firm->isUpdate(true)->save($_post, ['id'=>$id]);
            if($state !== false) {
                getAdminLog(" 编辑 企业审核列表 ID". $id);
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
            getAdminLog(" 删除 企业审核列表 ". $id);
            return success_json("提交成功");
        }
        return error_json("提交失败");
    }

    public function examine()
    {
        $id = request()->param('id');
        $Firm = model('Firm');
        $info = $Firm->where(['id'=>$id])->find();
        if(request()->isPost()){
            $_post = request()->post();
            $save = ['status' => $_post['status']];
            if($_post['status'] == 2){
                if($info['isweb'] == 1){
                    $save = ['status' => $_post['status']];
                    model('message')->isUpdate(false)->save([
                        'base_type' => 1,
                        'subdivide_type' => 7,
                        'uid' => $info['create_id'],
                        'title' => '系统消息',
                        'content' => '恭喜您，您提交的企业关联信息已通过',
                        'is_permanent' => 1,
                    ]);
                    // 企业审核通过，关联添加用户
                    model('card')->isUpdate(true)->save(['firm_id' => $info['id'], 'verify_status' => 0], ['uid' => $info['create_id']]);
                    getAdminLog(" 企业审核通过，关联添加用户 firm_id".$info['id']. " uid ". $info['create_id']);
                }
            }elseif ($_post['status'] == 3){
                if($info['isweb'] == 1){
                    $save = ['status' => $_post['status']];
                    model('message')->isUpdate(false)->save([
                        'base_type' => 1,
                        'subdivide_type' => 8,
                        'uid' => $info['create_id'],
                        'title' => '系统消息',
                        'content' => '企业入驻信息审核未通过，操作原因('.$_post['status_msg'].')',
                        'is_permanent' => 1,
                    ]);
                }
            }

            $state = $Firm->isUpdate(true)->save($save, ['id' => $id]);
            if($state !== false) {
                getAdminLog(" 审核 企业审核列表 ID".$id. " status ".$_post['status']);
                return success_json("提交成功");
            }
            return error_json("提交失败");
        }
        $info['business_type'] = explode('|', $info['business_type']);
        $info['industry'] = explode('|', $info['industry']);
        $info['region'] = explode('|', $info['region']);
        return view('', [
            'info' => $info,
        ]);
    }


}