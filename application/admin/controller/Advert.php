<?php
namespace app\admin\controller;

use app\admin\model\Advert as AdvertModel;
use app\admin\model\AdvertExposureStatDaily;
use think\Db;

class Advert extends Base
{
    public function _initialize()
    {
        $this->assign('meta_title', '广告列表');
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
            $show_status = request()->post('show_status');
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

            if(!empty($show_status)){
                array_push($map, 'show_status = ' . $show_status);
            }

            $where = "";
            if(count($map) > 0 ){
                $where = " where ". implode(' and ', $map);
            }

            $sql1 = "SELECT *,IF(start_time > $time, '1', if(end_time > $time, '2', '3')) as show_status FROM `mk_advert` WHERE `status` = 1  ORDER BY `show_status` ASC,`id`";
            $count = Db::query("select count(*) as cou from ($sql1) as t " . $where);
            $list = Db::query("select * from ($sql1) as t " . $where . " limit $offset, $limit");

            foreach ($list as $k=>$v){
                $v['key'] = $count[0]['cou'] - ($k + ($page - 1) * $limit);
                $v['adsense_title'] = allAdventFind($v['adsense_id']);
                $list[$k] = $v;
            }
            return json(['data'=>['count'=>$count[0]['cou'], 'list'=>$list]], 200);
        }
        $AdsenseAll = model('Adsense')->allselect();
        return view('', [
            'AdsenseAll' => $AdsenseAll,
        ]);
    }

    public function create()
    {
        $Advert = model('Advert');
        if(request()->isPost()) {
            $_post = request()->post();
            if(!empty($_post['start_time'])){
                $_post['start_time'] = strtotime($_post['start_time']);
            }
            if(!empty($_post['end_time'])){
                $_post['end_time'] = strtotime($_post['end_time']);
            }

            $state = $Advert->allowField(true)->data($_post)->save();

            if($state != false) {
                GetHttp(config('CacheHost') . config('CacheUrlApi')['0']);
                return success_json("提交成功");
            }
            return error_json("提交失败");
        }

        $adsenseAll = model('adsense')->allselect();
        return view('', [
            'adsenseAll' => $adsenseAll,
        ]);
    }

    public function edit()
    {
        $Advert = model('Advert');
        $id = request()->param('id');
        if(request()->isPost()) {
            $_post = request()->post();
            if(!empty($_post['start_time'])){
                $_post['start_time'] = strtotime($_post['start_time']);
            }
            if(!empty($_post['end_time'])){
                $_post['end_time'] = strtotime($_post['end_time']);
            }

            $state = $Advert->allowField(true)->isUpdate(true)->save($_post, ['id'=>$id]);

            if($state != false) {
                GetHttp(config('CacheHost') . config('CacheUrlApi')['0']);
                return success_json("提交成功");
            }
            return error_json("提交失败");
        }

        $info = $Advert->where(['id'=>$id])->find();
        $adsenseAll = model('adsense')->allselect();
        return view('', [
            'info' => $info->toArray(),
            'adsenseAll' => $adsenseAll,
        ]);
    }

    public function delete()
    {
        $Advert = model('Advert');
        $id = request()->param('id');
        $state = $Advert->isUpdate(true)->save(['status'=>0], ['id'=>$id]);
        if ($state != false) {
            GetHttp(config('CacheHost') . config('CacheUrlApi')['0']);
            return success_json("刪除成功");
        }
        return error_json("删除失败");
    }

    public function see()
    {
        if(request()->isPost()){

            $stime = request()->post('stime');
            $etime = request()->post('etime');
            $advert_id = request()->post('advert_id');

            $st = diffBetweenTwoDays($stime, $etime);
            $show_cnt = [];
            $click_pv = [];
            $click_uv_ip = [];
            $click_uv_uid = [];
            $xAxisData = [];

            for ($me = 0; $me <= $st; $me++) {
                array_push($xAxisData, date("Y-m-d", (strtotime($stime) + 86400 * $me)) );
                $ymd = date("Ymd", strtotime($stime) + 86400 * $me);
                $dataAdvert = AdvertExposureStatDaily::where(['advert_id' => $advert_id, 'ymd' => $ymd])->find();
                if($dataAdvert){
                    array_push($show_cnt, $dataAdvert['show_cnt']);
                    array_push($click_pv, $dataAdvert['click_pv']);
                    array_push($click_uv_ip, $dataAdvert['click_uv_ip']);
                    array_push($click_uv_uid, $dataAdvert['click_uv_uid']);
                } else {
                    array_push($show_cnt, 0);
                    array_push($click_pv, 0);
                    array_push($click_uv_ip, 0);
                    array_push($click_uv_uid, 0);
                }
            }
            return json([
                'code' => 0,
                'xAxisData' => $xAxisData,
                'show_cnt' => $show_cnt,
                'click_pv' => $click_pv,
                'click_uv_ip' => $click_uv_ip,
                'click_uv_uid' => $click_uv_uid,
            ]);
        }
        $id =  request()->param('id');
        $dateDay1 =  date("Y-m-d",strtotime("-1 day")) ;
        $dateDay8 =  date("Y-m-d",strtotime("-8 day")) ;
        $info = AdvertModel::where(['status'=>1, 'id'=>$id])->find();
        return view('', [
            'dateDay1' => $dateDay1,
            'dateDay8' => $dateDay8,
            'info' => $info,
        ]);
    }

}