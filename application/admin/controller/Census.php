<?php
namespace app\admin\controller;

class Census extends Base
{

    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        if(request()->isPost()){
            $userStat = model('userStat');
            $stime = request()->post('stime');
            $etime = request()->post('etime');

            $month_arr = [];
            $seriesUser = [];
            $st = $this->diffBetweenTwoDays($stime, $etime);
            /*$map['stat_date'] = ['between', [$stime, $etime]];
            $data = $userStat->where($map)->order('stat_date desc')->select();
            var_dump($data);exit();*/

            for ($me = 0; $me <= $st; $me++) {
                $ymd = date("Y-m-d", (strtotime($stime) + 86400 * $me));
                array_push($month_arr, $ymd);
                $stat_date = $userStat->where(['stat_date' => $ymd])->value('online_num');
                $stat_date = $stat_date  > 0 ? $stat_date : 0;
                array_push($seriesUser, $stat_date);

            }
            return json([
                'xAxisData' => $month_arr,
                'seriesUserData' => $seriesUser
            ]);

        }
        $dateDay1 =  date("Y-m-d",strtotime("-1 day")) ;
        $dateDay8 =  date("Y-m-d",strtotime("-8 day")) ;
        return view('', [
            'dataDay1' => $dateDay1,
            'dataDay8' => $dateDay8,
        ]);
    }

    public function diffBetweenTwoDays ($day1, $day2){
        $second1 = strtotime($day1);
        $second2 = strtotime($day2);
        if ($second1 < $second2) {
            $tmp = $second2;
            $second2 = $second1;
            $second1 = $tmp;
        }
        return ($second1 - $second2) / 86400;
    }



}