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

    ];

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $DataDic = model("DataDic");
        if(Request()->isPost()) {
            $map = ['status'=>1];
            $page = Request()->post('page');
            $limit = Request()->post('limit');
            $offset = ($page - 1) * $limit;
            $data_name = request()->post('data_name');
            $data_type_no = request()->post('data_type_no');
            if (!empty($data_name)) {
                $map['data_name'] = array('like', '%'.$data_name.'%');
            }
            if (!empty($data_type_no)) {
                $map['data_type_no'] = $data_type_no;
            }
            $data = $DataDic->where($map)->limit($offset, $limit)->order('data_type_no desc,sort desc,id desc')->select();
            $count = $DataDic->where($map)->count();
            foreach ($data as $k => $v) {
                if($v['data_type_no'] == 'RESOURCES_SUBDIVIDE'){
                    if(is_numeric($v['data_top_id'])) {
                        $DataInfo = $DataDic->where(['data_type_no'=>'RESOURCES_TYPE', 'data_no'=>$v['data_top_id']])->find();
                        if($DataInfo){
                            $v['data_name'] = '<span class="layui-border-blue layui-btn-xs">' . $DataInfo['data_name'] . '</span>-' . $v['data_name'];
                        }
                    }
                }

                if($v['data_type_no'] == 'REPORT_DETAIL_CAUSE'){
                    if(is_numeric($v['data_top_id'])) {
                        $DataInfo = $DataDic->where(['data_type_no'=>'REPORT_TYPE', 'data_no'=>$v['data_top_id']])->find();
                        if($DataInfo){
                            $v['data_name'] = '<span class="layui-border-blue layui-btn-xs">' . $DataInfo['data_name'] . '</span>-' . $v['data_name'];
                        }
                    }
                }
                if($v['data_type_no'] == 'RESOURCE_INDUSTRY') {
                    if(is_numeric($v['data_top_id'])) {
                        $DataInfo = $DataDic->where(['data_type_no'=>'RESOURCES_SUBDIVIDE', 'id'=>$v['data_top_id']])->find();
                        if($DataInfo){
                            $v['data_name'] = '<span class="layui-border-blue layui-btn-xs">' . $DataInfo['data_name'] . '</span>-' . $v['data_name'];
                        }
                    }
                }
                if($v['data_type_no'] == 'RESOURCE_INDUSTRY_SUBDIVIDE') {
                    if(is_numeric($v['data_top_id'])) {
                        $DataInfo = $DataDic->where(['data_type_no'=>'RESOURCE_INDUSTRY', 'data_no'=>$v['data_top_id']])->find();
                        if($DataInfo){
                            $v['data_name'] = '<span class="layui-border-blue layui-btn-xs">' . $DataInfo['data_name'] . '</span>-' . $v['data_name'];
                        }
                    }
                }

                $data[$k] = $v;
            }
            return json(['data' => ['count' => $count, 'list' => $data]], 200);
        }

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
            $_post   = request()->param();
            $state = $DataDic->save([
                'data_type_no' => $_post['data_type_no'],
                'data_type_name' => $this->data[$_post['data_type_no']]['title'],
                'data_name' => $_post['data_name'],
                'data_icon' => $_post['data_icon'],
                'sort' => $_post['sort'],
                'data_top_id' => $_post['data_top_id'],
                'data_dark_icon' => $_post['data_dark_icon'],
            ], ['id' => $id]);
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
            ['id'=>10,'pid'=>0,'title'=>'资源·货币','isedit'=>false],
            ['id'=>11,'pid'=>0,'title'=>'举报类型','isedit'=>false],
        ];
        $DataDic = model('DataDic');

        // 业务类型
        $this->DataDicAllUp($data, 4);


        $arrType = $DataDic->where(['status'=>1, 'data_type_no'=>'CONTACT_TYPE'])->select();
        foreach ($arrType as $k1 => $v1) {
            array_push($data, ['id' =>$v1['id'] ,'pid'=>2, 'title'=>$v1['data_name']]);
        }

        $arrCur = $DataDic->where(['status'=>1, 'data_type_no'=>'RESOURCE_CURRENCY'])->select();
        foreach ($arrCur as $k1 => $v1) {
            array_push($data, ['id' =>$v1['id'] ,'pid'=>10, 'title'=>$v1['data_name']]);
        }
        $dataAll = $DataDic->where(['status'=>1, 'data_type_no'=>'REPORT_TYPE'])->select();
        foreach ($dataAll as $k => $v) {
            array_push($data, ['id' =>$v['id'] ,'pid'=>11, 'title'=>$v['data_name']]);
            $all = $DataDic->where(['status'=>1, 'data_type_no'=>'REPORT_DETAIL_CAUSE', 'data_top_id'=>$v['data_no']])->select();
            foreach ($all as $k1 => $v1) {
                array_push($data, ['id' =>$v1['id'] ,'pid'=>$v['id'], 'title'=>$v1['data_name']]);
            }
        }
        return json(['code'=>0,'count'=>100,'data'=>$data], 200);
    }

    protected function DataDicAllUp(&$data, $level=0)
    {
        $DataDic = model('DataDic');
        $arrType = $DataDic->where(['status'=>1, 'data_type_no'=>'RESOURCES_TYPE'])->order('sort desc')->select();
        foreach ($arrType as $k1 => $v1) {
            array_push($data, ['id' => $v1['id'], 'pid' => 1, 'title' => '<span style=\'color:#FF5722;border:1px solid #FF5722;font-size:12px;padding: 2px 10px;line-height:30px;\'>资源·合作领域：</span>' . $v1['data_name'], 'data_no'=>$v1['data_no'], 'data_name'=>$v1['data_name'], 'data_icon'=>$v1['data_icon'], 'data_dark_icon'=>$v1['data_dark_icon'], 'sort'=>$v1['sort']]);
            if($level > 1) {
            $arrSub = $DataDic->where(['status'=>1, 'data_type_no'=>'RESOURCES_SUBDIVIDE','data_top_id'=>$v1['data_no']])->order('sort desc')->select();
            foreach ($arrSub as $k2 => $v2) {
                array_push($data, ['id' => $v2['id'], 'pid' => $v1['id'], 'title' =>"<span style='color:#009688;border:1px solid #009688;font-size:12px;padding: 2px 10px;line-height:30px;'>业务细分：</span>" . $v2['data_name'], 'data_no'=>$v1['data_no'], 'data_name'=>$v2['data_name'], 'data_icon'=>$v2['data_icon'], 'data_dark_icon'=>$v2['data_dark_icon'], 'sort'=>$v2['sort']]);
                if($level > 2) {
                    $arrInd = $DataDic->where(['status' => 1, 'data_type_no' => 'RESOURCE_INDUSTRY', 'data_top_id' => $v2['id']])->order('sort desc')->select();
                    foreach ($arrInd as $k3 => $v3) {
                        array_push($data, ['id' => $v3['id'], 'pid' => $v2['id'], 'title' =>"<span style='color:#1E9FFF;border:1px solid #1e9fff;font-size:12px;padding: 0 10px;line-height:30px;'>资源·行业类型</span>：" . $v3['data_name'], 'data_no'=>$v1['data_no'], 'data_name'=>$v3['data_name'], 'data_icon'=>$v3['data_icon'], 'data_dark_icon'=>$v3['data_dark_icon'], 'sort'=>$v3['sort']]);
                        if($level > 3) {
                            $arrIndSub = $DataDic->where(['status' => 1, 'data_type_no' => 'RESOURCE_INDUSTRY_SUBDIVIDE', 'data_top_id' => $v3['data_no']])->order('sort desc')->select();
                            foreach ($arrIndSub as $k4 => $v4) {
                                array_push($data, ['id' => $v4['id'], 'pid' => $v3['id'], 'title' => "<span style='color:#FFB800;border:1px solid #FFB800;font-size:12px;padding: 0 10px;line-height:30px;'>资源·行业细分</span>：" . $v4['data_name'], 'data_no'=>$v1['data_no'], 'data_name'=>$v4['data_name'], 'data_icon'=>$v4['data_icon'], 'data_dark_icon'=>$v4['data_dark_icon'], 'sort'=>$v4['sort']]);
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
