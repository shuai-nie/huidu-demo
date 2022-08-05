<?php

namespace app\admin\controller;

use think\Controller;
use think\Log;
use think\Request;
use util\Tree;

class Diction extends Base
{
    public $data = [
        'CONTACT_TYPE'     => ['type' => 'CONTACT_TYPE', 'title' => '联系方式'],
        'RESOURCES_TYPE'   => ['type' => 'RESOURCES_TYPE', 'title' => '资源·合作领域'],
        'RESOURCES_REGION' => ['type' => 'RESOURCES_REGION', 'title' => '资源·合作区域'],
        'RESOURCES_SUBDIVIDE' => ['type' => 'RESOURCES_SUBDIVIDE', 'title' => '资源·业务细分'],
        'REPORT_TYPE' => ['type'=>'REPORT_TYPE', 'title'=>'举报类型'],
        'REPORT_DETAIL_CAUSE' => ['type'=>'REPORT_DETAIL_CAUSE', 'title'=>'举报详细原因'],
        'RESOURCE_INDUSTRY' => ['type'=>'RESOURCE_INDUSTRY', 'title'=>'资源·行业类型'],
        'RESOURCE_INDUSTRY_SUBDIVIDE' => ['type'=>'RESOURCE_INDUSTRY_SUBDIVIDE', 'title'=>'资源·行业细分'],
        'RESOURCE_CURRENCY' => ['type'=>'RESOURCE_CURRENCY', 'title'=>'资源·货币'],
        'CONTACTS_INDUSTRY' => ['type'=>'CONTACTS_INDUSTRY', 'title'=>'人脉·行业'],

    ];

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        return view('', [
            'data_type' => $this->data,
            'meta_title' => '字典',
        ]);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        $DataDic = model('DataDic');
        if(request()->isPost()){
            $_post = request()->post();
            $Datafind = $DataDic->where(['data_type_no'=>$_post['data_type_no']])->order('data_no desc')->find();
            $state = $DataDic->save([
                'data_type_no' => $_post['data_type_no'],
                'data_type_name' => $this->data[$_post['data_type_no']]['title'],
                'data_no' => $Datafind['data_no']+1,
                'data_name' => $_post['data_name'],
                'data_icon' => $_post['data_icon'],
                'sort' => $_post['sort'],
                'data_top_id' => $_post['data_top_id'],
                'data_dark_icon' => $_post['data_dark_icon'],
                'url_keyword' => $_post['url_keyword'],
            ]);
            if($state !== false) {
                return success_json(lang('CreateSuccess', [lang('Dictionaries')] ));
            }
            return error_json(lang('CreateFail', [lang('Dictionaries')]) );
        }
        $resources = $DataDic->where(['data_type_no'=>'RESOURCES_TYPE','status'=>1])->field('data_type_no,data_top_id,data_no,data_name')->select();
        $reportDetailCauseAll = $DataDic->where(['data_type_no'=>'REPORT_DETAIL_CAUSE','status'=>1])->field('data_type_no,data_top_id,data_no,data_name')->select();
        return view('', [
            'typeData' => $this->data,
            'resources' => $resources,
            'reportDetailCauseAll' => $reportDetailCauseAll
        ]);
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $DataDic = model('DataDic');
        if(request()->isPost()) {
            $_post = request()->param();
            $state = $DataDic->where(['id' => $id])->update([
                'data_type_no' => $_post['data_type_no'],
                'data_type_name' => $this->data[$_post['data_type_no']]['title'],
                'data_name' => $_post['data_name'],
                'data_icon' => $_post['data_icon'],
                'sort' => $_post['sort'],
                'data_top_id' => $_post['data_top_id'],
                'data_dark_icon' => $_post['data_dark_icon'],
                'url_keyword' => $_post['url_keyword'],
            ]);
            if($state !== false) {
                return success_json(lang('EditSuccess', [lang('Dictionaries')] ));
            }
            return error_json(lang('EditFail', [lang('Dictionaries')]) );
        }
        $data = $DataDic->find($id);
        return view('', [
            'data' => $data,
            'typeData' => $this->data,
        ]);
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $DataDic = model('DataDic');
        $data = $DataDic->find($id);
        if($id != '') {
            $state = $DataDic->save(['status'=>0], ['id'=>$id]);
            if($state !== false) {
                switch ($data['data_type_no']) {
                    case 'RESOURCES_TYPE':
                        // 资源·业务类型
                        $this->deleteResourcesSubdivide($DataDic, $data['data_no']);

                        break;
                }
                return success_json(lang('DeleteSuccess', [lang('Dictionaries')] ));
            }
            return error_json(lang('DeleteFail', [lang('Dictionaries')]) );
        }
    }

    // 删除多条 资源·业务细分
    public function deleteResourcesSubdivide($DataDic, $id)
    {
        $industry = $DataDic->where(['status' => 1, 'data_type_no' => 'RESOURCES_SUBDIVIDE', 'data_top_id' => $id])->field('id,data_no,data_name,data_top_id')->select();
        foreach ($industry as $k => $v) {
            $DataDic->where(['id' => $v['id']])->update(['status' => 0]);
            Log::info($DataDic->getLastSql());
            $this->deleteResourcesIndustry($DataDic, $v['id']);
        }
    }

    // 删除多条 资源·行业类型
    protected function deleteResourcesIndustry($DataDic, $id)
    {
        // 资源·行业类型
        $industry = $DataDic->where(['status' => 1, 'data_type_no' => 'RESOURCE_INDUSTRY', 'data_top_id' => $id])->field('id,data_no,data_name,data_top_id')->select();
        foreach ($industry as $k => $v) {
            $DataDic->where(['id' => $v['id']])->update(['status' => 0]);
            Log::info($DataDic->getLastSql());
            $this->deleteResourcesIndustrySubdivide($DataDic, $v['data_no']);
        }
    }

    // 删除多条 资源·行业细分
    protected function deleteResourcesIndustrySubdivide($DataDic, $id)
    {
        $industry = $DataDic->where(['status' => 1, 'data_type_no' => 'RESOURCE_INDUSTRY_SUBDIVIDE', 'data_top_id' => $id])->field('id,data_no,data_name,data_top_id')->select();
        foreach ($industry as $k => $v) {
            $DataDic->where(['id' => $v['id']])->update(['status' => 0]);
            Log::info($DataDic->getLastSql());
        }
    }

    public function data_top_id()
    {
        $param = \request()->param();
        $DataDic = model('DataDic');
        $data = $DataDic->where(['data_type_no' => $param['data_type'], 'status' => 1])->select();
        if ($param['data_type'] == 'RESOURCE_INDUSTRY') {
            $allUp = [];
            $this->DataDicAllUp($allUp, 3);
            $all = Tree::toLayer($allUp, 1);
            $data = [];
            foreach ($all as $k => $v) {
                if (isset($v['child'])) {
                foreach ($v['child'] as $k1 => $v1){
                    if ( isset($v1['child'])) {
                    foreach ($v1['child'] as $k2 => $v2){
                        array_push($data, ['data_name'=>$v['data_name'] .'-' .$v1['data_name']  .'-' . $v2['data_name'], 'data_no'=> $v2['data_no']]);
                    }
                    }
                }
                }
            }
        } elseif ($param['data_type'] ==  'RESOURCES_SUBDIVIDE') {
            $allUp = [];
            $this->DataDicAllUp($allUp, 2);
            $all = Tree::toLayer($allUp, 1);
            $data = [];
            foreach ($all as $k => $v) {
                if (isset($v['child'])) {
                    foreach ($v['child'] as $k1 => $v1){
                        array_push($data, ['data_name'=>$v['data_name'] .'-' .$v1['data_name'] , 'data_no'=> $v1['data_no'], 'id'=>$v1['id']]);
                    }
                }
            }
        }
        return success_json('成功', $data);
    }

    public function read()
    {
        $data = [
            ['id'=>1,'pid'=>0,'title'=>'资源·合作领域','isedit'=>false],
            ['id'=>2,'pid'=>0,'title'=>'联系方式','isedit'=>false],
            ['id'=>3,'pid'=>0,'title'=>'资源·货币','isedit'=>false],
            ['id'=>4,'pid'=>0,'title'=>'举报类型','isedit'=>false],
            ['id'=>5,'pid'=>0,'title'=>'资源·合作区域','isedit'=>false],
            ['id'=>6,'pid'=>0,'title'=>'人脉·行业','isedit'=>false],
        ];
        $DataDic = model('DataDic');

        // 业务类型
        $this->DataDicAllUp($data, 4);

        $arrType = $DataDic->where(['status'=>1, 'data_type_no'=>'CONTACT_TYPE'])->select();
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
        }

        //人脉*行业
        $ContactsAll = $DataDic->where(['status'=>1, 'data_type_no'=>'CONTACTS_INDUSTRY'])->select();
        foreach ($ContactsAll as $k=>$v){
            array_push($data, ['id'=>$v['id'],'pid'=>6,'title'=>$v['data_name'], 'data_no'=>$v['data_no'], 'data_name'=>$v['data_name'], 'data_icon'=>$v['data_icon'], 'data_dark_icon'=>$v['data_dark_icon'], 'sort'=>$v['sort'], 'url_keyword'=>$v['url_keyword']]);
        }

        echo json_encode(['code'=>0, 'count'=>count($data),'data'=> $data ]);
    }

    protected function DataDicAllUp(&$data, $level=0)
    {
        $DataDic = model('DataDic');
        $arrType = $DataDic->where(['status'=>1, 'data_type_no'=>'RESOURCES_TYPE'])->order('sort desc')->select();
        foreach ($arrType as $k1 => $v1) {
            array_push($data, ['id' => $v1['id'], 'pid' => 1, 'title' => '资源·合作领域：' . $v1['data_name'], 'data_no'=>$v1['data_no'], 'data_name'=>$v1['data_name'], 'data_icon'=>$v1['data_icon'], 'data_dark_icon'=>$v1['data_dark_icon'], 'sort'=>$v1['sort'],'url_keyword'=>$v1['url_keyword']]);
            if($level > 1) {
            $arrSub = $DataDic->where(['status'=>1, 'data_type_no'=>'RESOURCES_SUBDIVIDE','data_top_id'=>$v1['data_no']])->order('sort desc')->select();
            foreach ($arrSub as $k2 => $v2) {
                array_push($data, ['id' => $v2['id'], 'pid' => $v1['id'], 'title' =>"业务细分：" . $v2['data_name'], 'data_no'=>$v2['data_no'], 'data_name'=>$v2['data_name'], 'data_icon'=>$v2['data_icon'], 'data_dark_icon'=>$v2['data_dark_icon'], 'sort'=>$v2['sort'],'url_keyword'=>$v2['url_keyword']]);
                if($level > 2) {
                    $arrInd = $DataDic->where(['status' => 1, 'data_type_no' => 'RESOURCE_INDUSTRY', 'data_top_id' => $v2['id']])->order('sort desc')->select();
                    foreach ($arrInd as $k3 => $v3) {
                        array_push($data, ['id' => $v3['id'], 'pid' => $v2['id'], 'title' =>"资源·行业类型：" . $v3['data_name'], 'data_no'=>$v3['data_no'], 'data_name'=>$v3['data_name'], 'data_icon'=>$v3['data_icon'], 'data_dark_icon'=>$v3['data_dark_icon'], 'sort'=>$v3['sort'],'url_keyword'=>$v3['url_keyword']]);
                        if($level > 3) {
                            $arrIndSub = $DataDic->where(['status' => 1, 'data_type_no' => 'RESOURCE_INDUSTRY_SUBDIVIDE', 'data_top_id' => $v3['data_no']])->order('sort desc')->select();
                            foreach ($arrIndSub as $k4 => $v4) {
                                array_push($data, ['id' => $v4['id'], 'pid' => $v3['id'], 'title' => "资源·行业细分：" . $v4['data_name'], 'data_no'=>$v4['data_no'], 'data_name'=>$v4['data_name'], 'data_icon'=>$v4['data_icon'], 'data_dark_icon'=>$v4['data_dark_icon'], 'sort'=>$v4['sort'],'url_keyword'=>$v4['url_keyword']]);
                            }
                        }
                    }
                }
            }
            }
        }
    }

    public function data()
    {
        $DataDic = model('DataDic');
        $data = $DataDic->where(['status' => 1, 'data_type_no' => 'RESOURCES_TYPE'])->field('id,data_no,data_name,data_top_id,data_type_name')->select();

        echo "<table cellpadding='1' cellspacing='1'>";
        foreach ($data as $k=> $v) {
            echo "<tr>";
            echo "<td>".$v['data_type_name']."</td>";
            echo "<td>".$v['id'] .'、' .$v['data_name']."Level1</td>";
            echo "</tr>";
            $s = $DataDic->where(['status' => 1, 'data_type_no' => 'RESOURCES_SUBDIVIDE', 'data_top_id'=>$v['data_no']])->field('id,data_no,data_name,data_top_id,data_type_name')->select();
            foreach ($s as $k1 => $v1 ) {
                echo "<tr>";
                echo "<td>".$v1['data_type_name']."</td>";
                echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;".$v1['id'] .'、' .$v1['data_name']."Level2</td>";
                echo "</tr>";
                $s2 = $DataDic->where(['status' => 1, 'data_type_no' => 'RESOURCE_INDUSTRY', 'data_top_id'=>$v1['id']])->field('id,data_no,data_name,data_top_id,data_type_name')->select();
                foreach ($s2 as $k2 => $v2 ) {
                    echo "<tr>";
                    echo "<td>".$v2['data_type_name']."</td>";
                    echo "<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$v2['data_name']."Level3</td>";
                    echo "</tr>";
                }
            }



        }
        echo "</table>";
    }




}
