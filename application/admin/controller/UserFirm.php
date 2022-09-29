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
        $FirmRelevanceDatum = model('FirmRelevanceDatum');
        $Firm = model('Firm');
        $User = model('User');
        $id = request()->param('id');

        if(request()->isPost()) {
            $_post = request()->post();
            $FirmRelevanceInfo = $FirmRelevance->where(['id'=>$id])->find();
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
                $save = ['status' => $_post['status']];
                model('message')->isUpdate(false)->save([
                    'base_type' => 1,
                    'subdivide_type' => 11,
                    'uid' => $FirmRelevanceInfo['uid'],
                    'title' => '系统消息',
                    'content' => '用户关联企业审核失败',
                    'is_permanent' => 1,
                ]);
            }

            $state = $FirmRelevance->isUpdate(true)->save($save, ['id'=>$id]);

            if($state !== false) {
                return  success_json('审核提交成功');
            }
            return error_json('审核提交失败');
        }

        $map = ['A.id'=>$id];
        $info = $FirmRelevance->alias('A')
            ->join($Firm->getTable()." B", "A.firm_id=B.id", "left")
            ->join($User->getTable()." C", "A.uid=C.id", "left")
            ->field('A.*,B.name as firm_name,C.username,C.nickname')
            ->where($map)->order('id desc')->find();
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

}