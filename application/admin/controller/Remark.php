<?php
namespace app\admin\controller;

use lib\Jurisdiction;
use think\Loader;
use think\View;
use app\admin\controller\Diction;
use think\Cache;

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
            'statistics' => $this->DataDicAllUp(),
        ]);
    }

    protected function DataDicAllUp()
    {
        if($data = Cache::get('name')){
            return $data;
        } else {
            $Resource = model('Resource');
            $DataDic = model('DataDic');
            $field = "id,data_type_no,data_type_name,data_top_id,data_no,data_name";
            $arrType = $DataDic->where(['status'=>1, 'data_type_no'=>'RESOURCES_TYPE'])->field($field)->order('sort desc')->select();
            foreach ($arrType as $k1 => $v1) {
                $v1['level'] = 1;
                $v1['data_type_name'] = '合作领域';
                $v1['count'] = $Resource->selectCount(['type' => $v1['data_no']]);
                $arrSub = $DataDic->where(['status'=>1, 'data_type_no'=>'RESOURCES_SUBDIVIDE','data_top_id'=>$v1['data_no']])->field($field)->order('sort desc')->select();
                foreach ($arrSub as $k2 => $v2) {
                    $v2['level'] = 2;
                    $v2['count'] = $Resource->selectCount(['business_subdivide' => $v2['data_no']]);;
                    $v1['data_type_name'] = '业务细分';
                    $arrInd = $DataDic->where(['status' => 1, 'data_type_no' => 'RESOURCE_INDUSTRY', 'data_top_id' => $v2['id']])->field($field)->order('sort desc')->select();
                    foreach ($arrInd as $k3 => $v3) {
                        $v3['level'] = 3;
                        $v1['data_type_name'] = '对应行业';
                        $v3['count'] = $Resource->selectCount(['industry'=>$v3['data_no']]);
                        $arrIndSub = $DataDic->where(['status' => 1, 'data_type_no' => 'RESOURCE_INDUSTRY_SUBDIVIDE', 'data_top_id' => $v3['data_no']])->field($field)->order('sort desc')->select();
                        foreach ($arrIndSub as $k4 => $v4) {
                            $v4['level'] = 4;
                            $v4['data_type_name'] = '行业细分';
                            $count = $Resource->selectSubdivideCount($v4['data_no']);
                            $v4['count'] = $count[0]['count_as'];
                            $arrIndSub[$k4] = $v4;
                        }
                        $v3['child'] = $arrIndSub;
                        $arrInd[$k3] = $v3;
                    }
                    $v2['child'] = $arrInd;
                    $arrSub[$k2] = $v2;
                }
                $v1['child'] = $arrSub;
                $arrType[$k1] = $v1;
            }

            $dataRegion = $DataDic->where(['status'=>1, 'data_type_no'=>'RESOURCES_REGION'])->field($field)->select();
            foreach ($dataRegion as $kNo => $valNo){
                $count = $Resource->selectRegionCount($valNo['data_no']);
                $valNo['level'] = 4;
                $valNo['count'] = $count[0]['count'];
                $valNo['child'] = array();
                $dataRegion[$kNo] = $valNo;
            }

            $data = array(
                array(
                    'data_type_name' => '资源*合作领域',
                    'data_name' => '',
                    'level' => 0,
                    'count' => 0,
                    'child' => $arrType
                ),
                array(
                    'data_type_name' => '资源*合作区域',
                    'data_name' => '',
                    'level' => 0,
                    'count' => 0,
                    'child' => $dataRegion
                ),
            );
            Cache::set('dataStatic', $data, 3600);
            return $data;
        }
    }

}