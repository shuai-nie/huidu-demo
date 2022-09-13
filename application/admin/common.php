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


function getAttribute($aid)
{
    $data = \app\admin\model\AdvertAttribute::where(['status'=>1, 'advert_id'=>$aid])->field('id,value')->select();
    $name = [];
    foreach ($data as $k => $v) {
        $data_name = \app\admin\model\DataDic::where(['status'=>1,'id'=>$v['value']])->value('data_name');
        array_push($name, $data_name);
    }
    return implode(',', $name);
}

function getFirmRelevanceDatumStatus($str)
{
    $FirmRelevanceDatum = model('FirmRelevanceDatum');
    return $FirmRelevanceDatum->type[$str];
}

function getFirmScale($str, $type_no){
    $DataDic = model('DataDic');
    $data = $DataDic->findType(['data_type_no' => $type_no, 'data_no' => $str]);
    return $data->data_name;
}
