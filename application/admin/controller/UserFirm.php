<?php
namespace app\admin\controller;

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

            $count = $FirmRelevance->alias('A')
                ->join($Firm->getTable()." B", "A.firm_id=B.id", "left")
                ->join($User->getTable()." C", "A.uid=C.id", "left")
                ->where($map)->count();
            $exp = new \think\Db\Expression('field(A.status, 0,1,2), id desc');
            $data = $FirmRelevance->alias('A')
                ->join($Firm->getTable()." B", "A.firm_id=B.id", "left")
                ->join($User->getTable()." C", "A.uid=C.id", "left")
                ->field('A.*,B.name as firm_name,C.username,C.nickname')
                ->where($map)->order($exp)->limit($offset, $limit)->select();

            return json(['data'=>['count'=>$count, 'list'=>$data]], 200);
        }
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
                    'content' => '用户关联企业审核失败',
                    'is_permanent' => 1,
                ]);
            }elseif ($_post['status'] == 1) {
                model('message')->isUpdate(false)->save([
                    'base_type' => 1,
                    'subdivide_type' => 11,
                    'uid' => $FirmRelevanceInfo['uid'],
                    'title' => '系统消息',
                    'content' => '用户关联企业审核成功',
                    'is_permanent' => 1,
                ]);
               model('Card')->isUpdate(true)->save(['verify_status' => 1, 'firm_id' => $info['firm_id']], ['uid' => $info['uid']]);
            }

            $state = $FirmRelevance->isUpdate(true)->save($save, ['id' => $id]);

            if($state !== false) {
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
                    'content' => '用户关联企业审核成功',
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
            return success_json('提交成功');
        }

        $firmAll = model('firm')->where(['status' => 2])->select();
        return view('', [
            'firmAll' => $firmAll,
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