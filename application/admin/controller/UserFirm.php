<?php
namespace app\admin\controller;

use think\Db;

class UserFirm extends Base
{
    public function _initialize()
    {
        $this->assign('meta_title', '用户关联企业审核');
        parent::_initialize();
    }

    public function index()
    {
        if(request()->isPost()) {
            $FirmRelevance = model('FirmRelevance');
            $Firm = model('Firm');
            $User = model('User');

            $map = [];
            $page = request()->post('page', 1);
            $limit = request()->post('limit');
            $offset = ($page - 1 ) * $limit;
            $username = request()->post('username');
            $firm_name = request()->post('firm_name');

            if(!empty($username)) {
                $map['C.username|C.nickname'] = ['like', '%'.$username.'%'];
            }

            if(!empty($firm_name)) {
                $map['B.name'] = ['like', '%'.$firm_name.'%'];
            }

            $Card = model('Card');
            $count = $FirmRelevance->alias('A')
                ->join($Firm->getTable()." B", "A.firm_id=B.id", "left")
                ->join($User->getTable()." C", "A.uid=C.id", "left")
                ->join($Card->getTable()." D", "(A.uid=D.uid and D.status =1)", "left")
                ->where($map)->count();
            $exp = new \think\Db\Expression('field(A.status, 0,1,2), id desc');
            $data = $FirmRelevance->alias('A')
                ->join($Firm->getTable()." B", "A.firm_id=B.id", "left")
                ->join($User->getTable()." C", "A.uid=C.id", "left")
                ->join($Card->getTable()." D", "(A.uid=D.uid and D.status =1)", "left")
                ->field('A.*,B.name as firm_name,C.username,C.nickname,D.business_tag,D.position')
                ->where($map)->order($exp)->limit($offset, $limit)->select();

            $DataDic = model('DataDic');
            foreach ($data as $key => $val){

                if($val['business_tag']){
                    $business_tag = explode('|', $val['business_tag']);
                    $tag = array();
                    foreach ($business_tag as $k => $v) {
                        $resources = $DataDic->where(['data_type_no'=>'RESOURCES_TYPE','status'=>1,'data_no'=>$v])->field('data_type_no,data_top_id,data_no,data_name')->find();
                        if(isset($resources['data_name'])) {
                            array_push($tag, $resources['data_name'] );
                        }
                    }
                    $val['business_tag'] = implode('|', $tag);
                }
                $val['userZ'] = $Card->where(['firm_id'=>$val['firm_id'], 'status'=>1])->count();
                $val['userC'] = $Card->where(['firm_id'=>$val['firm_id'], 'status'=>1, 'verify_status'=>1])->count();
                $data[$key] = $val;
            }

            return json(['data'=>['count'=>$count, 'list'=>$data]], 200);
        }
        getAdminLog("查看用户关联企业审核列表");
        return view('', []);
    }

    public function examine()
    {
        $FirmRelevance = model('FirmRelevance');

        $Firm = model('Firm');
        $User = model('User');

        $id = request()->param('id');
        $map = ['A.id'=>$id];
        $info = $FirmRelevance->alias('A')
            ->join($Firm->getTable()." B", "A.firm_id=B.id", "left")
            ->join($User->getTable()." C", "A.uid=C.id", "left")
            ->field('A.*,B.name as firm_name,C.username,C.nickname')
            ->where($map)->order('id desc')->find();

        if(request()->isPost()) {
            $_post = request()->post();
            $FirmRelevanceInfo = $FirmRelevance->where(['id' => $id])->find();
            $save = ['status' => $_post['status']];
            if($_post['status'] == 2){
                $save = ['status' => $_post['status'], 'feedback'=>$_post['status_msg']];
                model('message')->isUpdate(false)->save([
                    'base_type' => 1,
                    'subdivide_type' => 10,
                    'uid' => $FirmRelevanceInfo['uid'],
                    'title' => '系统消息',
                    'content' => '用户关联企业审核未通过，操作原因('.$_post['status_msg'].')',
                    'is_permanent' => 1,
                ]);
            }elseif ($_post['status'] == 1) {
                model('message')->isUpdate(false)->save([
                    'base_type' => 1,
                    'subdivide_type' => 11,
                    'uid' => $FirmRelevanceInfo['uid'],
                    'title' => '系统消息',
                    'content' => '恭喜您，您提交的企业关联信息已通过',
                    'is_permanent' => 1,
                ]);
               model('Card')->isUpdate(true)->save(['verify_status' => 1, 'firm_id' => $info['firm_id']], ['uid' => $info['uid']]);
            }

            $state = $FirmRelevance->isUpdate(true)->save($save, ['id' => $id]);

            if($state !== false) {
                getAdminLog(" 用户关联企业审核 ID".$id);
                return  success_json('审核提交成功');
            }
            return error_json('审核提交失败');
        }

        $FirmRelevanceDatum = model('FirmRelevanceDatum');
        $datumAll = $FirmRelevanceDatum->where(['firm_relevance_id'=>$id])->select();
        foreach ($datumAll as $k => $v) {
            if($v['type'] == 4 || $v['type'] == 5 ){
                $v['value'] = explode('|', $v['value']);
            }
            $datumAll[$k] = $v;
        }
        return view('', [
            'info' => $info,
            'datumAll' => $datumAll,
        ]);
    }

    public function create()
    {
        if(request()->isPost()){
            $_post = request()->post();
            $firmRelevance = model('firmRelevance');
            $firmRelevanceDatum = model('firmRelevanceDatum');
            $count = $firmRelevance->where(['uid'=>$_post['uid'], 'status'=>[['=', 0], ['=', 1], 'or']])->count();
            if($count > 0){
                return error_json('用户已关联，请修改UID');
            }

            $userInfo = model('User')->where(['id'=>$_post['uid']])->find();
            if(empty($userInfo)){
                return error_json('用户不存在，请修改UID');
            }

            $firmRelevance->isUpdate(false)->data([
                'uid' => $_post['uid'],
                'firm_id' => $_post['firm_id'],
                'status' => $_post['status']
            ])->save();

            if($_post['status'] == 1){
                model('message')->isUpdate(false)->save([
                    'base_type' => 1,
                    'subdivide_type' => 11,
                    'uid' => $_post['uid'],
                    'title' => '系统消息',
                    'content' => '恭喜您，您提交的企业关联信息已通过',
                    'is_permanent' => 1,
                ]);
                model('Card')->isUpdate(true)->save(['verify_status' => 1, 'firm_id' => $_post['firm_id']], ['uid' => $_post['uid']]);
            }


            $firmRelevanceId = $firmRelevance->id;
            $time = time();

            $save = [];
            if(!empty($_post['type_1'])){
                array_push($save, ['firm_relevance_id'=>$firmRelevanceId, 'type'=>1, 'value'=>$_post['type_1'], 'create_time' => $time]);
            }

            if(!empty($_post['type_2'])){
                array_push($save, ['firm_relevance_id'=>$firmRelevanceId, 'type'=>2, 'value'=>$_post['type_2'], 'create_time' => $time]);
            }

            if(!empty($_post['type_3'])){
                array_push($save, ['firm_relevance_id'=>$firmRelevanceId, 'type'=>3, 'value'=>$_post['type_3'], 'create_time' => $time]);
            }

            if(!empty($_post['type_4'])){
                array_push($save, ['firm_relevance_id'=>$firmRelevanceId, 'type'=>4, 'value'=>$_post['type_4'], 'create_time' => $time]);
            }

            if(!empty($_post['type_5'])){
                array_push($save, ['firm_relevance_id'=>$firmRelevanceId, 'type'=>5, 'value'=>$_post['type_5'], 'create_time' => $time]);
            }
            if(!empty($save)){
                $firmRelevanceDatum->isUpdate(false)->allowField(true)->saveAll($save, false);
            }
            getAdminLog("添加用户关联企业审核". $firmRelevanceId);
            return success_json('提交成功');
        }

        $firmAll = model('firm')->where(['status' => 2])->select();
        return view('', [
            'firmAll' => $firmAll,
        ]);
    }

    public function edit()
    {
        $id = request()->param('id');
        $Card = model('Card');
        $firmRelevance = model('firmRelevance');
        $firmRelevanceDatum = model('firmRelevanceDatum');
        if(request()->isPost()){
            $_post = request()->post();

            $state = false;
            Db::startTrans();

            try {
                $firmRelevance->isUpdate(true)->save([
                    'firm_id' => $_post['firm_id'],
                    'status' => $_post['status'],
                    'feedback' => $_post['status_msg'],
                ], ['id' => $id]);

                if($_post['status'] == 1){
                    $Card->isUpdate(true)->save(['verify_status' => 1], ['uid' => $_post['uid']]);
                }else{
                    $Card->isUpdate(true)->save(['verify_status'=>0], ['uid' => $_post['uid']]);
                }

                if(isset($_post['type_1']) && !empty($_post['type_1'])){
                    $count = $firmRelevanceDatum->where(['firm_relevance_id' => $id, 'type' => 1])->count();
                    if($count > 0 ){
                        $firmRelevanceDatum->isUpdate(true)->save(['value'=>$_post['type_1']], ['firm_relevance_id' => $id, 'type' => 1]);
                    }else{
                        $firmRelevanceDatum->isUpdate(false)->data(['firm_relevance_id' => $id, 'type' => 1, 'value' => $_post['type_1']])->save();
                    }
                }

                if(isset($_post['type_2']) && !empty($_post['type_2'])){
                    $count = $firmRelevanceDatum->where(['firm_relevance_id' => $id, 'type' => 2])->count();
                    if($count > 0 ){
                        $firmRelevanceDatum->isUpdate(true)->save(['value'=>$_post['type_2']], ['firm_relevance_id' => $id, 'type' => 2]);
                    }else{
                        $firmRelevanceDatum->isUpdate(false)->data(['firm_relevance_id' => $id, 'type' => 2, 'value' => $_post['type_2']])->save();
                    }
                }

                if(isset($_post['type_3']) && !empty($_post['type_3'])){
                    $count = $firmRelevanceDatum->where(['firm_relevance_id' => $id, 'type' => 3])->count();
                    if($count > 0 ){
                        $firmRelevanceDatum->isUpdate(true)->save(['value'=>$_post['type_3']], ['firm_relevance_id' => $id, 'type' => 3]);
                    }else{
                        $firmRelevanceDatum->isUpdate(false)->data(['firm_relevance_id' => $id, 'type' => 3, 'value' => $_post['type_3']])->save();
                    }
                }

                if(isset($_post['type_4']) && !empty($_post['type_4'])){
                    $count = $firmRelevanceDatum->where(['firm_relevance_id' => $id, 'type' => 4])->count();
                    if($count > 0 ){
                        $firmRelevanceDatum->isUpdate(true)->save(['value'=>$_post['type_4']], ['firm_relevance_id' => $id, 'type' => 4]);
                    }else{
                        $firmRelevanceDatum->isUpdate(false)->data(['firm_relevance_id' => $id, 'type' => 4, 'value' => $_post['type_4']])->save();
                    }
                }

                if(isset($_post['type_5']) && !empty($_post['type_5'])){
                    $count = $firmRelevanceDatum->where(['firm_relevance_id' => $id, 'type' => 5])->count();
                    if($count > 0 ){
                        $firmRelevanceDatum->isUpdate(true)->save(['value'=>$_post['type_5']], ['firm_relevance_id' => $id, 'type' => 5]);
                    }else{
                        $firmRelevanceDatum->isUpdate(false)->data(['firm_relevance_id' => $id, 'type' => 5, 'value' => $_post['type_5']])->save();
                    }
                }
                $state = true;

                Db::commit();
            }catch (Exception $e) {
                Db::rollback();
            }

            if($state != false) {
                getAdminLog(" 编辑 用户关联企业审核 ". $id);
                return success_json('提交成功');
            }
            return error_json('提交失败');
        }

        $firmRelevanceInfo = $firmRelevance->where(['id'=>$id])->find();
        $firmAll = model('firm')->where(['status' => 2])->select();
        $userInfo = model('User')->where(['id'=>$firmRelevanceInfo['uid']])->find();
        $firmRelevanceDatumAll = $firmRelevanceDatum->where(['firm_relevance_id' => $firmRelevanceInfo['id']])->select();
        $DatumAll = [];
        foreach ($firmRelevanceDatumAll as $val){
            if($val['type'] == 1){
                $DatumAll[1] = [
                    $val['value'],
                    $val['id'],
                ];
            }
            if($val['type'] == 2){
                $DatumAll[2] = [
                    $val['value'],
                    $val['id'],
                ];
            }
            if($val['type'] == 3){
                $DatumAll[3] = [
                    $val['value'],
                    $val['id'],
                ];
            }
            if($val['type'] == 4){
                $DatumAll[4] = [
                    $val['value'],
                    $val['id'],
                ];
            }
            if($val['type'] == 5){
                $DatumAll[5] = [
                    $val['value'],
                    $val['id'],
                ];
            }
        }
        return view('', [
            'firmAll' => $firmAll,
            'firmRelevanceInfo' => $firmRelevanceInfo,
            'userInfo' => $userInfo,
            'DatumAll' => $DatumAll
        ]);

    }

    public function get_user()
    {
        $uid = request()->param('uid');
        $firmRelevance = model('firmRelevance');
        $count = $firmRelevance->where(['uid'=>$uid, 'status'=>[['=', 0], ['=', 1], 'or']])->count();
        if($count > 0){
            return success_json('用户已关联，请修改UID');
        }

        $userInfo = model('User')->where(['id'=>$uid])->find();
        if(!empty($userInfo)){
            return error_json('用户可以关联', $userInfo);
        }else{
            return success_json('用户不存在，请修改UID');
        }

    }

}