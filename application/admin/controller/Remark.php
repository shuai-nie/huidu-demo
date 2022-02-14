<?php
namespace app\admin\controller;

use lib\Jurisdiction;
use think\Loader;
use think\View;

class Remark extends Base{

    public function index()
    {
        $list = model('AdminLog')->order('id desc')->limit(10)->select();
        $CounselorCount = model('Counselor')->where(['status'=>1])->count();
        $ResourceCount = model('Resource')->where(['status'=>1])->count();
        $AdvertisementCount = model('Advertisement')->where(['status'=>1])->count();
        $UserCount = model('User')->where(['status'=>1])->count();
        return view('', [
            'list' => $list,
            'CounselorCount' => $CounselorCount,
            'ResourceCount' => $ResourceCount,
            'AdvertisementCount' => $AdvertisementCount,
            'UserCount' => $UserCount,
        ]);
    }
}