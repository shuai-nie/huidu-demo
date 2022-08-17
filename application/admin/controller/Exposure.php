<?php
namespace app\admin\controller;

use app\admin\model\Advert;
use app\admin\model\AdvertExposureStatDaily;

class Exposure extends Base
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        if(request()->isPost()){

            $stime = request()->post('stime');
            $etime = request()->post('etime');

            $show_cnt_req = request()->post('show_cnt');
            $click_pv_req = request()->post('click_pv');
            $click_uv_ip_req = request()->post('click_uv_ip');
            $click_uv_uid_req = request()->post('click_uv_uid');

            $checkbox = explode(',',  request()->post('checkbox'));
            $xAxisData = [];
            $st = diffBetweenTwoDays($stime, $etime);
            $data = [];

            for ($me = 0; $me <= $st; $me++) {
                $ymd = date("Y-m-d", (strtotime($stime) + 86400 * $me));
                array_push($xAxisData, $ymd);
            }

            foreach ($checkbox as $v){
                $show_cnt = [];
                $click_pv = [];
                $click_uv_ip = [];
                $click_uv_uid = [];
                for ($me = 0; $me <= $st; $me++) {
                    $ymd = date("Ymd", (strtotime($stime) + 86400 * $me));
                    $dataAdvert = AdvertExposureStatDaily::where(['advert_id' => $v, 'ymd' => $ymd])->find();
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

                $dataInsert = [
                    'title' => Advert::allFind($v)->title,
                ];

                if($show_cnt_req){
                    $dataInsert['show_cnt'] = $show_cnt;
                }

                if($click_pv_req){
                    $dataInsert['click_pv'] = $click_pv;
                }

                if($click_uv_ip_req){
                    $dataInsert['click_uv_ip'] = $click_uv_ip;
                }

                if($click_uv_uid_req){
                    $dataInsert['click_uv_uid'] = $click_uv_uid;
                }

                array_push($data, $dataInsert);
            }

            return json([
                'code' => 0,
                'xAxisData' => $xAxisData,
                'data' => $data,
            ]);
        }

        $dateDay1 =  date("Y-m-d",strtotime("-1 day")) ;
        $dateDay8 =  date("Y-m-d",strtotime("-8 day")) ;

        $advertAll = \app\admin\model\Advert::selectData();
        return view('', [
            'dataDay1' => $dateDay1,
            'dataDay8' => $dateDay8,
            'advertAll' => $advertAll,
        ]);
    }

}