<?php
namespace app\admin\controller;

use app\admin\model\Advert as AdvertModel;
use app\admin\model\AdvertExposureStatDaily;

class Advert extends Base
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        $Advert = model('Advert');
        if(request()->isPost()){
            $limit = request()->post('limit');
            $page = request()->post('page', 1);
            $title = request()->post('title');
            $adsense_id = request()->post('adsense_id');
            $start_time = request()->post('start_time');
            $end_time = request()->post('end_time');
            $offset = ($page - 1) * $limit;
            $map = ['status'=>1];

            if(!empty($title)) {
                $map['title'] = ['like', "%{$title}%"];
            }

            if(!empty($adsense_id)) {
                $map['adsense_id'] = $adsense_id;
            }

            if(!empty($start_time)) {
                $map['start_time'] = ['>=', strtotime($start_time)];
            }

            if(!empty($end_time)) {
                $map['end_time'] = ['<=', strtotime($end_time)];
            }

            $count = $Advert->where($map)->count();
            $list = $Advert->where($map)->order('id desc')->limit($offset, $limit)->select();
            foreach ($list as $k=>$v){
                $v['key'] = $count-($k+ ($page-1)*$limit);
                $v['adsense_title'] = allAdventFind($v['adsense_id']);
                $v['show_status'] = getAdvertShowStatus($v['start_time'], $v['end_time']);
                $list[$k] = $v;
            }
            return json(['data'=>['count'=>$count, 'list'=>$list]], 200);
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