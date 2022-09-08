<?php
namespace app\admin\controller;

class UserFirm extends Base
{
    public function _initialize()
    {
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

            $count = $FirmRelevance->alias('A')
                ->join($Firm->getTable()." B", "A.firm_id=B.id", "left")
                ->join($User->getTable()." C", "A.uid=C.id", "left")
                ->where($map)->count();

            $data = $FirmRelevance->alias('A')
                ->join($Firm->getTable()." B", "A.firm_id=B.id", "left")
                ->join($User->getTable()." C", "A.uid=C.id", "left")
                ->field('A.*,B.name as firm_name,C.username,C.nickname')
                ->where($map)->order('id desc')->limit($offset, $limit)->select();

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