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

    public function statistics()
    {
        $data = [
            ['id'=>1,'pid'=>0,'data_name'=>'资源·合作领域','isedit'=>false],
            ['id'=>2,'pid'=>0,'data_name'=>'联系方式','isedit'=>false],
            ['id'=>3,'pid'=>0,'data_name'=>'资源·货币','isedit'=>false],
            ['id'=>4,'pid'=>0,'data_name'=>'举报类型','isedit'=>false],
            ['id'=>5,'pid'=>0,'data_name'=>'资源·合作区域','isedit'=>false],
        ];
        $DataDic = model('DataDic');

        // 业务类型
        $bb = $this->DataDicAllUp();
        dump($bb);exit();
        return $data;

        /*$arrType = $DataDic->where(['status'=>1, 'data_type_no'=>'CONTACT_TYPE'])->select();
        foreach ($arrType as $k1 => $v1) {
            array_push($data, ['id' =>$v1['id'] ,'pid'=>2, 'title'=>$v1['data_name'],'data_no'=>$v1['data_no'], 'data_name'=>$v1['data_name'], 'data_icon'=>$v1['data_icon'], 'data_dark_icon'=>$v1['data_dark_icon'], 'sort'=>$v1['sort'], 'url_keyword'=>$v1['url_keyword']]);
        }

        $arrCur = $DataDic->where(['status'=>1, 'data_type_no'=>'RESOURCE_CURRENCY'])->select();
        foreach ($arrCur as $k1 => $v1) {
            array_push($data, ['id' =>$v1['id'] ,'pid'=>3, 'title'=>$v1['data_name'],'data_no'=>$v1['data_no'], 'data_name'=>$v1['data_name'], 'data_icon'=>$v1['data_icon'], 'data_dark_icon'=>$v1['data_dark_icon'], 'sort'=>$v1['sort'], 'url_keyword'=>$v1['url_keyword']]);
        }
        $dataAll = $DataDic->where(['status'=>1, 'data_type_no'=>'REPORT_TYPE'])->select();
        foreach ($dataAll as $k => $v) {
            array_push($data, ['id' =>$v['id'] ,'pid'=>4, 'title'=>$v['data_name'], 'data_no'=>$v['data_no'], 'data_name'=>$v['data_name'], 'data_icon'=>$v['data_icon'], 'data_dark_icon'=>$v['data_dark_icon'], 'sort'=>$v['sort'], 'url_keyword'=>$v['url_keyword']]);
            $all = $DataDic->where(['status'=>1, 'data_type_no'=>'REPORT_DETAIL_CAUSE', 'data_top_id'=>$v['data_no']])->select();
            foreach ($all as $k1 => $v1) {
                array_push($data, ['id' =>$v1['id'] ,'pid'=>$v['id'], 'title'=>$v1['data_name'],'data_no'=>$v1['data_no'], 'data_name'=>$v1['data_name'], 'data_icon'=>$v1['data_icon'], 'data_dark_icon'=>$v1['data_dark_icon'], 'sort'=>$v1['sort'], 'url_keyword'=>$v1['url_keyword']]);
            }
        }

        $resourcesAll = $DataDic->where(['status'=>1, 'data_type_no'=>'RESOURCES_REGION'])->select();
        foreach ($resourcesAll as $k => $v) {
            array_push($data, ['id' =>$v['id'] ,'pid'=>5, 'title'=>$v['data_name'], 'data_no'=>$v['data_no'], 'data_name'=>$v['data_name'], 'data_icon'=>$v['data_icon'], 'data_dark_icon'=>$v['data_dark_icon'], 'sort'=>$v['sort'], 'url_keyword'=>$v['url_keyword']]);
        }*/

    }

    protected function DataDicAllUp()
    {
        if($data = Cache::get('name')){
            return $data;
        } else {
            $DataDic = model('DataDic');

            $field = "id,data_type_no,data_type_name,data_top_id,data_no,data_name";
            $arrType = $DataDic->where(['status'=>1, 'data_type_no'=>'RESOURCES_TYPE'])->field($field)->order('sort desc')->select();
            $arrTypeCountZ = $DataDic->where(['status'=>1, 'data_type_no'=>'RESOURCES_TYPE'])->count();
            foreach ($arrType as $k1 => $v1) {
                $arrTypeCount = $DataDic->where(['status'=>1, 'data_type_no'=>'RESOURCES_SUBDIVIDE','data_top_id'=>$v1['data_no']])->count();
                $v1['level'] = 1;
                $v1['count'] = $arrTypeCount;
                $arrSub = $DataDic->where(['status'=>1, 'data_type_no'=>'RESOURCES_SUBDIVIDE','data_top_id'=>$v1['data_no']])->field($field)->order('sort desc')->select();
                foreach ($arrSub as $k2 => $v2) {
                    $count = $DataDic->where(['status' => 1, 'data_type_no' => 'RESOURCE_INDUSTRY', 'data_top_id' => $v2['id']])->count();
                    $v2['level'] = 2;
                    $v2['count'] = $count;
                    $arrInd = $DataDic->where(['status' => 1, 'data_type_no' => 'RESOURCE_INDUSTRY', 'data_top_id' => $v2['id']])->field($field)->order('sort desc')->select();
                    foreach ($arrInd as $k3 => $v3) {
                        $count = $DataDic->where(['status' => 1, 'data_type_no' => 'RESOURCE_INDUSTRY_SUBDIVIDE', 'data_top_id' => $v3['data_no']])->count();
                        $v3['level'] = 3;
                        $v3['count'] = $count;
                        $arrIndSub = $DataDic->where(['status' => 1, 'data_type_no' => 'RESOURCE_INDUSTRY_SUBDIVIDE', 'data_top_id' => $v3['data_no']])->field($field)->order('sort desc')->select();
                        foreach ($arrIndSub as $k4 => $v4) {
                            $v4['level'] = 4;
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

            $dataTypeNo = $DataDic->where(['status'=>1, 'data_type_no'=>'CONTACT_TYPE'])->field($field)->select();
            $dataTypeNoCount = $DataDic->where(['status'=>1, 'data_type_no'=>'CONTACT_TYPE'])->count();
            $dataCurrency = $DataDic->where(['status'=>1, 'data_type_no'=>'RESOURCE_CURRENCY'])->field($field)->select();
            $dataCurrencyCount = $DataDic->where(['status'=>1, 'data_type_no'=>'RESOURCE_CURRENCY'])->count();
            $dataType = $DataDic->where(['status'=>1, 'data_type_no'=>'REPORT_TYPE'])->field($field)->select();
            $dataTypeCount = $DataDic->where(['status'=>1, 'data_type_no'=>'REPORT_TYPE'])->count();
            $dataRegion = $DataDic->where(['status'=>1, 'data_type_no'=>'RESOURCES_REGION'])->field($field)->select();
            $dataRegionCount = $DataDic->where(['status'=>1, 'data_type_no'=>'RESOURCES_REGION'])->count();

            foreach ($dataTypeNo as $kNo => $valNo){
                $valNo['level'] = 4;
                $valNo['count'] = 0;
                $valNo['child'] = array();
                $dataTypeNo[$kNo] = $valNo;
            }

            foreach ($dataCurrency as $kNo => $valNo){
                $valNo['level'] = 4;
                $valNo['count'] = 0;
                $valNo['child'] = array();
                $dataCurrency[$kNo] = $valNo;
            }

            foreach ($dataType as $kNo => $valNo){
                $valNo['level'] = 4;
                $valNo['count'] = 0;
                $valNo['child'] = array();
                $dataType[$kNo] = $valNo;
            }

            foreach ($dataRegion as $kNo => $valNo){
                $valNo['level'] = 4;
                $valNo['count'] = 0;
                $valNo['child'] = array();
                $dataRegion[$kNo] = $valNo;
            }

            $data = array(
                array(
                    'data_type_name' => '资源*合作领域',
                    'data_name' => '',
                    'level' => 0,
                    'count' => $arrTypeCountZ,
                    'child' => $arrType
                ),
                array(
                    'data_type_name' => '联系方式',
                    'data_name' => '',
                    'level' => 0,
                    'count' => $dataTypeNoCount,
                    'child' => $dataTypeNo
                ),
                array(
                    'data_type_name' => '资源货币',
                    'data_name' => '',
                    'level' => 0,
                    'count' => $dataCurrencyCount,
                    'child' => $dataCurrency
                ),
                array(
                    'data_type_name' => '举报类型',
                    'data_name' => '',
                    'level' => 0,
                    'count' => $dataTypeCount,
                    'child' => $dataType
                ),
                array(
                    'data_type_name' => '资源*合作区域',
                    'data_name' => '',
                    'level' => 0,
                    'count' => $dataRegionCount,
                    'child' => $dataRegion
                ),
            );
            Cache::set('dataStatic', $data, 3600);
            return $data;

        }
    }

}