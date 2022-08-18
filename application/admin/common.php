<?php

function allAdventFind($id)
{
    $data = model('Adsense')->allFind($id);
    return $data['title'];
}

function diffBetweenTwoDays ($day1, $day2){
    $second1 = strtotime($day1);
    $second2 = strtotime($day2);
    if ($second1 < $second2) {
        $tmp = $second2;
        $second2 = $second1;
        $second1 = $tmp;
    }
    return ($second1 - $second2) / 86400;
}

function getAdvertShowStatus($sta, $end)
{
    $time = time();
    if($sta > $time){
        return "未上架";
    }
    if($sta <  $time) {
        if($end > $time) {
            return "正常";
        }else {
            return "已过期";
        }
    }

}