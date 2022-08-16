<?php
namespace app\admin\controller;

use app\admin\model\Advert;

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
            $checkbox = explode(',',  request()->post('checkbox'));


            $xAxisData = [];
            $seriesUser = [];
            $st = diffBetweenTwoDays($stime, $etime);
            $data = [];
            foreach ($checkbox as $v){

                $seriesData = [];
                for ($me = 0; $me <= $st; $me++) {
                    $ymd = date("Y-m-d", (strtotime($stime) + 86400 * $me));
                    array_push($xAxisData, $ymd);
                }

                array_push($data, [
                    'title' => Advert::allFind($v)->title,
                ]);
            }
            var_dump($data);

            exit();

//            for ($me = 0; $me <= $st; $me++) {
//                $ymd = date("Y-m-d", (strtotime($stime) + 86400 * $me));
//                array_push($month_arr, $ymd);
//                $stat_date = 0; //$userStat->where(['stat_date' => $ymd])->value('online_num');
//                $stat_date = $stat_date  > 0 ? $stat_date : 0;
////                array_push($seriesUser, $stat_date);
//            }

//            return json([
//                'xAxisData' => $month_arr,
//                'seriesUserData' => $seriesUser
//            ]);
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