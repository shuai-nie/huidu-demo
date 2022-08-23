<?php
namespace app\admin\controller;

use think\Db;

class AdsenseData extends Base
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        $Advert = model('Advert');
        if(request()->isPost()){
            $time = time();
            $limit = request()->post('limit');
            $page = request()->post('page', 1);
            $title = request()->post('title');
            $adsense_id = request()->post('adsense_id');
            $start_time = request()->post('start_time');
            $end_time = request()->post('end_time');
            $offset = ($page - 1) * $limit;
            $map = [];

            if(!empty($title)) {
                array_push($map, " `title` like '%{$title}%'");
            }

            if(!empty($adsense_id)) {
                array_push($map, " `adsense_id` = $adsense_id ");
            }

            if(!empty($start_time)) {
                array_push($map,'start_time >= '.strtotime($start_time) );
            }

            if(!empty($end_time)) {
                array_push($map, 'end_time <= '. strtotime($end_time) );
            }

            $where = "";
            if(count($map) > 0 ){
                $where = "where ". implode(' and ', $map);
            }

            $sql1 = "SELECT *,IF(start_time > $time, '1', if(end_time > $time, '2', '3')) as show_status FROM `mk_advert` WHERE `status` = 1  ORDER BY FIELD(show_status, 2,1,3) ";
            $count = Db::query("select count(*) as cou from ($sql1) as t  " . $where);
            $list = Db::query("select * from ($sql1) as t  " . $where . " limit $offset, $limit");

            foreach ($list as $k=>$v){
                $v['key'] = $count[0]['cou'] - ($k + ($page - 1) * $limit);
                $v['adsense_title'] = allAdventFind($v['adsense_id']);
                $v['attribute_title'] = getAttribute($v['id']);
                $list[$k] = $v;
            }
            return json(['data'=>['count'=>$count[0]['cou'], 'list'=>$list]], 200);
        }
        $AdsenseAll = model('Adsense')->allselect();
        return view('/advert/index', [
            'AdsenseAll' => $AdsenseAll,
            'type' => 'data',
        ]);

    }
}