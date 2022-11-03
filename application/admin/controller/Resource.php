<?php

namespace app\admin\controller;

use think\Config;
use think\Controller;
use think\Request;
use think\db\Expression;

class Resource extends Base
{
    public $ty = [
        1 => '我提供',
        2 => '我需求',
    ];

    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $examine = \request()->get('type');
        $DataDic = model('DataDic');
        if (\request()->isPost()) {
            $map      = ['A.status' => 1];
            $page     = \request()->post('page');
            $limit    = \request()->post('limit', Config::get('paginate')['list_rows']);
            $offset   = ($page - 1) * $limit;
            $uid      = \request()->post('uid');
            $username = \request()->post('username');
            $field = \request()->post('field');
            $order = \request()->post('order');
            $types = \request()->post('types');
            if(!empty($field) && !empty($order)) {
                $order = new Expression('A.auth=2 desc ,'.'A.' . $field . ' ' . $order);
            } else {
                $order = new Expression('A.auth=2 desc, A.id desc ');
            }
            if (!empty($uid)) {
                $map['A.uid'] = $uid;
            }
            if (!empty($username)) {
                $map['B.username'] = ['like', "%{$username}%"];
            }
            $title = \request()->post('title');
            if (!empty($title)) {
                $map['A.title'] = ['like', "%{$title}%"];
            }
            $auth = \request()->post('auth');
            if (!empty($auth)) {
                $map['A.auth'] = $auth;
            }
            $ty   = \request()->post('ty');
            $type = \request()->post('type');
            $home_roll = \request()->post('home_roll');
            if (!empty($ty)) {
                $map['A.ty'] = $ty;
            }
            if (is_numeric($type)) {
                $map['A.type'] = $type;
            }
            if(is_numeric($home_roll)){
                $map['A.home_roll'] = $home_roll;
            }

            if(is_numeric($types)){
                $map['types'] = $types;
            }

            $data  = model("Resource")->alias('A')
                ->join(model('User')->getTable() . " B", "A.uid=B.id", 'left')
                ->where($map)->field('A.*,B.username')
                ->order($order)->limit($offset, $limit)->select();

            $count = model("Resource")->alias('A')
                ->join(model('User')->getTable() . " B", "A.uid=B.id", 'left')
                ->where($map)->count();

            foreach ($data as $key => $value) {
                $type      = explode('|', $value['type']);
                $valueType = [];
                foreach ($type as $val) {
                    if (is_numeric($val)) {
                        $ValuesType = $DataDic->field('id,data_name')->where(['data_type_no' => 'RESOURCES_TYPE', 'data_no' => $val, 'status' => 1])->find();
                        array_push($valueType, !empty($ValuesType) ? $ValuesType['data_name'] : '');
                    }
                }
                $value['type'] = implode('|', $valueType);

                if ($value['region'] == '|') {
                    $value['region'] = '不限';
                } else {
                    $region      = explode('|', $value['region']);
                    $valueRegion = [];
                    foreach ($region as $val) {
                        if (is_numeric($val)) {
                            $ResourcesType = $DataDic->field('id,data_name')->where(['data_type_no' => 'RESOURCES_REGION', 'data_no' => $val, 'status' => 1])->find();
                            array_push($valueRegion, !empty($ResourcesType) ? $ResourcesType['data_name'] : '');
                        }
                    }
                    $value['region'] = implode('|', $valueRegion);
                }
                if ($value['business_subdivide']) {
                    $subdivide = explode('|', $value['business_subdivide']);
                    $valueSu   = array();
                    foreach ($subdivide as $val) {
                        if (is_numeric($val)) {
                            $ResourcesSu = $DataDic->field('id,data_name')->where(['data_type_no' => 'RESOURCES_SUBDIVIDE', 'data_no' => $val, 'status' => 1])->find();
                            array_push($valueSu, !empty($ResourcesSu) ? $ResourcesSu['data_name'] : '');
                        }
                    }
                    $value['business_subdivide'] = implode('|', $valueSu);
                }

                $data[$key] = $value;
            }
            return json(['data' => ['count' => $count, 'list' => $data]], 200);
        }
        $type = $DataDic->field('data_no,data_name')->where(['data_type_no' => 'RESOURCES_TYPE', 'status' => 1])->select();
        return view('', [
            'ty' => $this->ty,
            'type' => $type,
            'meta_title' => $examine == 'examine' ? '资源审核' : '资源管理',
            'examine' => $examine,
        ]);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        if (request()->isPost()) {
            $_post = request()->post();

            $_post['img'] = isset($_post['img']) ? implode('|', $_post['img']) : '';
            $_post['region'] = isset($_post['region']) ? implode('|', $_post['region']) : '';
            $_post['industry_subdivide'] = isset($_post['industry_subdivide']) ? implode('|', $_post['industry_subdivide']) : '';
            $_post['business_subdivide'] = $_post['subdivide'];
            $_post['top_start_time'] = !empty($_post['top_start_time']) ? strtotime($_post['top_start_time']) : 0;
            $_post['top_end_time'] = !empty($_post['top_end_time']) ? strtotime($_post['top_end_time']) : 0;
            unset($_post['subdivide']);
            $_post['types']      = 2;
            $_post['flush_time'] = time();
            $_post['intro'] = isset($_post['intro']) ? htmlspecialchars_decode($_post['intro']) : '';
            if (($_post['auth'] == 1 || $_post['auth'] == 2) && $_post['ty'] == 1) {
                $this->userpublish($_post['uid']);
            }

            $Resource = model('Resource');
            $temp = $_post;
            unset($_post['temp']);
            $state = $Resource->save($_post);
            if ($state !== false) {
                $ResourceId = $Resource->id;
                $this->resource_from_table($temp, $ResourceId);
                if (($_post['auth'] == 1 || $_post['auth'] == 2) && $_post['ty'] == 1) {
                    $userInfo         = model('UserInfo')->where(['uid' => $_post['uid']])->find();
                    $UserRechargeFind = model('UserRecharge')->find($userInfo['user_recharge_id']);
                    // 加
                    model('UserRecharge')->where(['id' => $userInfo['user_recharge_id']])->setInc('used_publish');
                    model('PackageLog')->save([
                        'uid'         => $_post['uid'],
                        'type'        => 1,
                        'recharge_id' => $userInfo['user_recharge_id'],
                        'package_id'  => $UserRechargeFind['package_id'],
                        'resource_id' => $ResourceId,
                        'remarks'     => '新建',
                        'state'       => 1,
                    ]);
                }
                if(($_post['auth'] == 1 || $_post['auth'] == 2) && $_post['ty'] == 2){
                    model('card')->isUpdate(true)->save(['quality'=>1], ['uid'=>$_post['uid']]);
                }

                return success_json(lang('CreateSuccess', [lang('Resource')]));
            }
            return error_json(lang('CreateFail', [lang('Resource')]));
        }
        $this->DataDicAssign();
        return view('');
    }

    protected function DataDicAssign()
    {
        $DataDic         = model('DataDic');
        $resourcesType   = $DataDic->where(['data_type_no' => 'RESOURCES_TYPE', 'status' => '1'])->order('sort desc')->select();
        $resourcesRegion = $DataDic->where(['data_type_no' => 'RESOURCES_REGION', 'status' => '1'])->order('sort desc')->select();
        $DataDicData     = $DataDic->where(['data_type_no' => 'CONTACT_TYPE', 'status' => 1])->order('sort desc')->select();
        $Subivde         = $DataDic->where(['data_type_no' => 'RESOURCES_SUBDIVIDE', 'status' => 1, 'data_top_id' => $resourcesType[0]['data_no']])->order('sort desc')->select();
        $resourceIndustry = $DataDic->where(['data_type_no'=> 'RESOURCE_INDUSTRY', 'status' => 1])->select();
        if(isset($resourceIndustry[0]['data_no'])){
        $resourceIndustrySubdivide = $DataDic->where(['data_type_no' => 'RESOURCE_INDUSTRY_SUBDIVIDE', 'status' => 1, 'data_top_id' => $resourceIndustry[0]['data_no']])->order('sort desc')->select();
        } else {
            $resourceIndustrySubdivide = array();
        }
        $this->assign('resourcesType', $resourcesType);
        $this->assign('resourcesRegion', $resourcesRegion);
        $this->assign('DataDicData', $DataDicData);
        $this->assign('ty', $this->ty);
        $this->assign('Subivde', $Subivde);
        $this->assign('resourceIndustry', $resourceIndustry);
        $this->assign('resourceIndustrySubdivide', $resourceIndustrySubdivide);
    }

    protected function resource_from_table($_post, $resource_id)
    {
        $ResourceFormTemplate = model('ResourceFormTemplate');
        $ResourceForm = model('ResourceForm');
        if(isset($_post['temp'])){
            $temp = $_post['temp'];
            $k = 0;
            foreach ($temp as $key => $value){
                $template = $ResourceFormTemplate->field('form_type,fill_flag')->find($key);
                if($template['form_type'] == 4){
                    $timeArr = $value['time'];
                    $arr[$k]['content'] = implode('|', $timeArr);
                } elseif ($template['form_type'] == 0 || $template['form_type'] == 1 || $template['form_type'] == 2 || $template['form_type'] == 3 ){
                    $arr[$k]['content'] = $value;
                } elseif ($template['form_type'] == 5){
                    $arr[$k]['currency_type'] = $_post['currency_type'];
                    $arr[$k]['content'] = $value;
                }
                $arr[$k]['resource_id'] = $resource_id;
                $arr[$k]['form_template_id'] = $key;
                $k++;
            }
            $ResourceForm->where(['resource_id'=>$resource_id])->delete();
            $ResourceForm->saveAll($arr);
        }
        return true;
    }

    private function userpublish($uid)
    {
        $UserRecharge = model('UserRecharge');
        $UserCount    = $UserRecharge->alias('A')->join(model('UserInfo')->getTable() . " B", "A.id=B.user_recharge_id")
            ->where(['B.uid' => $uid])->field("A.publish,A.used_publish")->find();

        if ($UserCount['publish'] <= $UserCount['used_publish']) {
            echo json_encode([
                'msg'  => '用户发布次数已用完',
                'code' => 400,
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }
        return true;
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param int $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $Resource     = model('Resource');
        $resourceInfo = $Resource->find($id);
        if (request()->isPost()) {
            $_post = request()->post();

            $_post['img'] = isset($_post['img']) ? implode('|', $_post['img']) : '';
            $_post['region'] = isset($_post['region']) ? implode('|', $_post['region']) : '';
            $_post['business_subdivide'] = isset($_post['subdivide']) ? $_post['subdivide'] : '';
            $_post['top_start_time'] = !empty($_post['top_start_time']) ? strtotime($_post['top_start_time']) : 0;
            $_post['top_end_time'] = !empty($_post['top_end_time']) ? strtotime($_post['top_end_time']) : 0;
            $_post['industry_subdivide'] = isset($_post['industry_subdivide']) ? implode('|', $_post['industry_subdivide']) : '';

            $_post['intro'] = isset($_post['intro']) ? htmlspecialchars_decode($_post['intro']) : '';
            if ($resourceInfo['auth'] != $_post['auth'] && ($_post['auth'] == 1 || $_post['auth'] == 2) && $_post['ty'] == 1 && ($resourceInfo['auth'] == 3 || $resourceInfo['auth'] == 4 || $resourceInfo['auth'] == 5)) {
                $this->userpublish($_post['uid']);
            }
            $temp = $_post;
            unset($_post['temp']);
            $state = $Resource->save($_post, ['id' => $id]);
            if ($state !== false) {
                $this->resource_from_table($temp, $id);
                $userInfo = model('UserInfo')->where(['uid' => $_post['uid']])->find();
                $UserRechargeFind = model('UserRecharge')->find($userInfo['user_recharge_id']);
                if ($resourceInfo['auth'] != $_post['auth'] && ($_post['auth'] == 1 || $_post['auth'] == 2) && $_post['ty'] == 1 && ($resourceInfo['auth'] == 3 || $resourceInfo['auth'] == 4 || $resourceInfo['auth'] == 5)) {
                    // 加
                    model('UserRecharge')->where(['id' => $userInfo['user_recharge_id']])->setInc('used_publish');
                    model('PackageLog')->save([
                        'uid'         => $_post['uid'],
                        'type'        => 1,
                        'recharge_id' => $userInfo['user_recharge_id'],
                        'package_id'  => $UserRechargeFind['package_id'],
                        'resource_id' => $id,
                        'remarks'     => '编辑',
                        'state'       => 1,
                    ]);

                } else if ($resourceInfo['auth'] != $_post['auth'] && ($_post['auth'] == 3 || $_post['auth'] == 4 || $_post['auth'] == 5) && $_post['ty'] == 1) {
                    // 减
                    model('UserRecharge')->where(['id' => $userInfo['user_recharge_id']])->setDec('used_publish');
                    model('PackageLog')->save([
                        'uid'         => $_post['uid'],
                        'type'        => 1,
                        'recharge_id' => $userInfo['user_recharge_id'],
                        'package_id'  => $UserRechargeFind['package_id'],
                        'resource_id' => $id,
                        'remarks'     => '编辑',
                        'state'       => 2,
                    ]);
                }

                if( ($_post['auth'] == 1 || $_post['auth'] == 2) && $_post['ty'] == 2){
                    model('card')->isUpdate(true)->save(['quality'=>1], ['uid'=>$_post['uid']]);
                }

                return success_json(lang('EditSuccess', [lang('Resource')]));
            }
            return error_json(lang('EditFail', [lang('Resource')]));
        }

        $resourceInfo['img'] = explode('|', $resourceInfo['img']);
        if ($resourceInfo['region'] == '|') {
            $resourceInfo['region'] = array('|');
        } else {
            $resourceInfo['region'] = explode('|', $resourceInfo['region']);
        }
        $resourceInfo['top_start_time'] = $resourceInfo['top_start_time'] > 10000 ? date('Y-m-d H:i:s', $resourceInfo['top_start_time']) : '';
        $resourceInfo['top_end_time'] = $resourceInfo['top_end_time'] > 10000 ? date('Y-m-d H:i:s', $resourceInfo['top_end_time']) : '';

        $DataDic = model('DataDic');
        $Subivde     = $DataDic->where(['data_type_no' => 'RESOURCES_SUBDIVIDE', 'status' => 1, 'data_top_id' => $resourceInfo['type']])->order('sort desc')->select();

        if (isset($resourceInfo['business_subdivide'][0])) {
            $RESOURCES   = $DataDic->where(['data_type_no' => 'RESOURCES_SUBDIVIDE', 'status' => 1])->find();
            $data_top_id = $RESOURCES['data_top_id'];
        } else {
            $data_top_id = $Subivde[0]['data_no'];
        }
        $resourceInfo['industry_subdivide'] = explode('|', $resourceInfo['industry_subdivide']);
        $this->DataDicAssign();
        return view('', [
            'resource'        => $resourceInfo,
            'ty'              => $this->ty,
            'Subivde'         => $Subivde,
            'data_top_id'     => $data_top_id,
        ]);
    }

    protected function res($data)
    {

    }

    public function topping($id)
    {
        $Resource = model('Resource');
        if (\request()->isPost()) {
            $_post                   = \request()->post();
            $_post['top_start_time'] = !empty($_post['top_start_time']) ? strtotime($_post['top_start_time']) : '';
            $_post['top_end_time']   = !empty($_post['top_end_time']) ? strtotime($_post['top_end_time']) : '';
            $state                   = $Resource->save($_post, ['id' => $id]);
            if ($state !== false) {
                return success_json(lang('EditSuccess', [lang('Resource')]));
            }
            return error_json(lang('EditFail', [lang('Resource')]));
        }

        $data                   = $Resource->find($id);
        $data['top_start_time'] = $data['top_start_time'] > 10000 ? date('Y-m-d H:i:s', $data['top_start_time']) : '';
        $data['top_end_time']   = $data['top_end_time'] > 10000 ? date('Y-m-d H:i:s', $data['top_end_time']) : '';
        return view('', ['data' => $data]);
    }

    /**
     * 删除指定资源
     * @param int $id
     * @return \think\Response
     */
    public function delete($id)
    {
        if ($id != '') {
            $resourceInfo = model('Resource')->find($id);
            $state        = model('Resource')->save(['status' => 0], ['id' => $id]);
            if ($state !== false) {
                if (($resourceInfo['auth'] == 1 || $resourceInfo['auth'] == 2) && $resourceInfo['ty'] == 1) {
                    $userInfo         = model('UserInfo')->where(['uid' => $resourceInfo['uid']])->find();
                    $UserRechargeFind = model('UserRecharge')->find($userInfo['user_recharge_id']);
                    // 减
                    model('UserRecharge')->where(['id' => $userInfo['user_recharge_id']])->setDec('used_publish');
                    model('PackageLog')->save([
                        'uid'         => $resourceInfo['uid'],
                        'type'        => 1,
                        'recharge_id' => $userInfo['user_recharge_id'],
                        'package_id'  => $UserRechargeFind['package_id'],
                        'resource_id' => $id,
                        'remarks'     => '删除',
                        'state'       => 2,
                    ]);
                }
                return success_json(lang('DeleteSuccess', [lang('Resource')]));
            }
            return error_json(lang('DeleteFail', [lang('Resource')]));
        }
    }

    public function toplist()
    {
        if (Request()->isPost()) {
            $map    = ['A.status' => 1, 'A.auth' => 1];
            $page   = Request()->post('page');
            $limit  = Request()->post('limit');
            $offset = ($page - 1) * $limit;
            $uid    = \request()->post('uid');
            if (!empty($uid)) {
                $map['A.uid'] = $uid;
            }
            $title = \request()->post('title');
            if (!empty($title)) {
                $map['A.title'] = ['like', "%{$title}%"];
            }
            $data  = model("Resource")->alias('A')
                ->join(model('User')->getTable() . " B", "A.uid=B.id", 'left')
                ->where($map)->field('A.*,B.username')
                ->order('A.top_end_time desc,A.id desc')->limit($offset, $limit)->select();
            $count = model("Resource")->alias('A')
                ->join(model('User')->getTable() . " B", "A.uid=B.id", 'left')
                ->where($map)->count();
            return json(['data' => ['count' => $count, 'list' => $data]], 200);
        }
        return view('', [
            'meta_title' => '资源置顶',
        ]);
    }

    public function flush()
    {
        if (\request()->isPost()) {
            $id    = \request()->post('id');
            $state = model("Resource")->save(['flush_time' => time()], ['id' => $id]);
            if ($state !== false) {
                return success_json('刷新成功', ['time' => time()]);
            }
            return error_json('刷新失败');
        }
    }

    public function subdivide()
    {
        if (\request()->isPost()) {
            $topId   = \request()->post('data_top_id');
            $DataDic = model('DataDic');
            $data    = $DataDic->where(['data_type_no' => 'RESOURCES_SUBDIVIDE', 'status' => 1, 'data_top_id' => $topId])->field('data_type_no,data_type_name,data_no,data_name')->order('sort desc')->select();
            return success_json('成功', ['data' => $data]);
        }
    }

    public function industry()
    {
        if (\request()->isPost()) {
            $topId   = \request()->post('top_id');
            $DataDic = model('DataDic');
            $data = $DataDic->where(['data_type_no' => 'RESOURCES_SUBDIVIDE', 'status' => 1, 'data_no' =>$topId])->find();
            $dataAll = $DataDic->where(['data_type_no' => 'RESOURCE_INDUSTRY', 'status' => 1, 'data_top_id' => $data['id']])->field('data_type_no,data_type_name,data_no,data_name')->order('sort desc')->select();
            return success_json('成功', ['data' => $dataAll]);
        }
    }


    public function roll($id)
    {
        if (\request()->isPost()) {
            $name  = \request()->post('name');
            $value = \request()->post('value');
            $state = model("Resource")->save([$name => $value], ['id' => $id]);
            if ($state !== false) {
                return success_json('修改成功');
            }
            return error_json('修改失败');
        }
    }

    public function fromhtmlvalue()
    {
        $resourceId = \request()->post('resourceId');
        $Resource = model('Resource');
        $ResourceForm = model('ResourceForm');
        $ResourceFormTemplate = model('ResourceFormTemplate');
        $DataDic = model('DataDic');
        $ResourceInfo = $Resource->find($resourceId);
        $template = $ResourceFormTemplate->where(['status' => 1, 'business_subdivide' => $ResourceInfo['business_subdivide'], 'ty' => ['in', "0,".$ResourceInfo['ty']] ])->order('sort desc')->select();

        $html = "";
        $container = false;
        if($template){
            foreach ($template as $key => $value) {
                if($value['form_type'] != 6 && $value['form_type'] != 7) {
                $html .= "<div class=\"layui-form-item\">\n".
                    "        <label class=\"layui-form-label\">" . $value['form_title'] . "</label>\n";
                }
                $ResourceFormInfo = $ResourceForm->where(['resource_id' => $ResourceInfo['id'], 'form_template_id' => $value['id']])->find();
                switch ($value['form_type']){
                    case 0:
                        $html .= "<div class=\"layui-input-block\" >";
                        $html .= "<input type=\"text\" name=\"temp[{$value['id']}]\" value='{$ResourceFormInfo['content']}' placeholder=\"请输入\" autocomplete=\"off\" class=\"layui-input\">";
                        $html .= "   </div>";;
                        break;
                    case 1:
                        $html .= "<div class=\"layui-input-block\" >";
                        $html .= "<textarea placeholder=\"请输入内容\" name='temp[{$value['id']}]' class=\"layui-textarea\">{$ResourceFormInfo['content']}</textarea>";
                        $html .= "   </div>";;
                        break;
                    case 2:
                        $html .= "<div class=\"layui-input-block\" >";
                        $html .= "<input type=\"number\" name=\"temp[{$value['id']}]\" value='{$ResourceFormInfo['content']}' placeholder=\"请输入\" autocomplete=\"off\" class=\"layui-input\">";
                        $html .= "   </div>";;
                        break;
                    case 3:
                        $html .= "<div class=\"layui-input-block\" >";
                        $html .= "<input type=\"text\" name=\"temp[{$value['id']}]\" value='{$ResourceFormInfo['content']}' placeholder=\"请输入\" autocomplete=\"off\" class=\"layui-input\">";
                        $html .= "   </div>";;
                        break;
                    case 4:
                        $html .= "<div class=\"layui-input-block\" >";
                        $html .= "<div class=\"layui-input-inline\" style='width:80px;' >";
                        $content = explode('|',  $ResourceFormInfo['content']);
                        if(count($content) != 4) {
                            $content = ['', '', '', ''];
                        }

                        $html .= "<input type=\"number\" name=\"temp[{$value['id']}][time][0]\" id='time_{$value['id']}' value='{$content[0]}' placeholder=\"请输入\" autocomplete=\"off\" class=\"layui-input\">"
                            . "</div>\n"
                            . "<div class=\"layui-form-mid\" style=''>:</div>"
                            . "<div class=\"layui-input-inline\" style='width:80px;' >"
                            . "<input type=\"number\" name=\"temp[{$value['id']}][time][1]\" placeholder=\"请输入\" value='{$content[1]}' autocomplete=\"off\" class=\"layui-input\" />"
                            . "</div>"
                            . "<div class=\"layui-form-mid\" style=''>-</div>"
                            . "<div class=\"layui-input-inline\" style='width:80px;'>"
                            . "<input type=\"number\" name=\"temp[{$value['id']}][time][2]\" placeholder=\"\"  value='{$content[2]}' autocomplete=\"off\" class=\"layui-input\" />"
                            . "</div>"
                            . "<div class=\"layui-form-mid\" style=''>:</div>"
                            . "<div class=\"layui-input-inline\" style='width:80px;' >"
                            . "<input type=\"number\" name=\"temp[{$value['id']}][time][3]\" placeholder=\"\"  value='{$content[3]}' autocomplete=\"off\" class=\"layui-input\" />"
                            . "</div>";

                        $html .= "</div>";
                        $html .= "   </div>";
                        break;
                    case 5:
                        $html .= "<div class=\"layui-input-block\" >";
                        $html .= "<div class=\"layui-input-inline\" style='' >";
                        $html .= "<select name=\"currency_type\">";
                        $html .= "<option value=\"currency_type\">请选择</option>";
                        $DataDicInfo = $DataDic->where(['data_type_no'=>'RESOURCE_CURRENCY', 'status'=>1])->field('data_no,data_name,data_icon')->select();
                        foreach ($DataDicInfo as $k2 => $v2){
                            if($ResourceFormInfo['currency_type'] == $v2['data_no']){
                                $html .= "<option value=\"{$v2['data_no']}\" selected >{$v2['data_name']}</option>";
                            }else {
                                $html .= "<option value=\"{$v2['data_no']}\" >{$v2['data_name']}</option>";
                            }
                        }
                        $html .= "</select>";
                        $html .= "</div>";
                        $html .= "<div class=\"layui-input-inline\" style='' >";
                        $html .= "<input type=\"number\" name=\"temp[{$value['id']}]\" value='{$ResourceFormInfo['content']}' placeholder=\"请输入\" autocomplete=\"off\" class=\"layui-input\">";
                        $html .= "</div>";
                        $html .= "</div>";
                        break;
                    case 6:
//                        $html .= "<div class=\"layui-input-block\" >";
//                        $container = true;
//                        $html .= "<script id=\"container\" name=\"intro\" type=\"text/plain\">{$ResourceInfo['intro']}</script>";
//                        $html .= "   </div>";;
                        break;
                    case 7:
//                        $html .= '<button type="button" class="layui-btn" id="test7submit">产品图片</button>'
//                            . '<blockquote class="layui-elem-quote layui-quote-nm" style="margin: 10px;">'
//                            . '预览图：'
//                            . '<div class="layui-upload-list" id="demo7">'
//                            . '';
//                        if(!empty($ResourceInfo['img'])){
//                            $img = explode('|', $ResourceInfo['img']);
//                            foreach ($img as $kl =>$vl){
//                                $html .= "<div style=\"width:100px;float:left;position:relative;margin:10px;\">"
//                                ."<i class=\"layui-icon layui-icon-close img-delete\" style=\"color:red;border:1px solid red;position:absolute;right:0px;top:-20px;\"></i>"
//                                ."<img width=\"100\" height=\"100\" src=\"{$vl}\" class=\"layui-upload-img\">"
//                                ."<input type=\"hidden\" name=\"img[{$kl}]\" value=\"{$vl}\"></div>";
//                            }
//                        }
//                        $html .= '</div><div style="clear:both;"></div>' . '</blockquote>';
                        break;
                    case 8:
                        $html .= '<button type="button" class="layui-btn" id="test8submit">logo</button>'
                            . '<input type="hidden" name="logo" value="'.$ResourceInfo['logo'].'" />'
                            . '<blockquote class="layui-elem-quote layui-quote-nm" style="margin: 10px;">'
                            . '预览图：'
                            . '<div class="layui-upload-list" id="demo8">';
                        if(!empty($ResourceInfo['logo'])){
                            $html .= "<img src='{$ResourceInfo['logo']}' height='100' width='100' />";
                        }
                            $html .= '</div><div style="clear:both;"></div></blockquote>';
                        break;
                    default:
                        break;
                }
                $html .= "   </div>";
            }
//            if($container == false) {
//                $html .= "<div class=\"layui-input-block\" >";
//                $container = true;
//                $html .= "<script id=\"container\" name=\"intro\" type=\"text/plain\">{$ResourceInfo['intro']}</script>";
//                $html .= "   </div>";
//                $html .= "   </div>";
//            }
            return success_json('成功', ['html'=>$html,'container'=>$container]);
        } else {
            return error_json('沒有模板');
        }
        exit();
    }

    public function fromhtml()
    {
        $tyId = \request()->post('tyId');
        $fromId = \request()->post('fromId');
        $ResourceFormTemplate = model('ResourceFormTemplate');
        $DataDic = model('DataDic');
        $template = $ResourceFormTemplate->where(['status' => 1, 'business_subdivide' => $fromId, 'ty' => ['in', "0,".$tyId]])->order('sort desc')->select();

        $html = "";
        $container = false;
        if($template){
            foreach ($template as $key => $value) {
                if($value['form_type'] != 6 && $value['form_type'] != 7) {
                $html .= "<div class=\"layui-form-item\">\n".
                    "        <label class=\"layui-form-label\">" . $value['form_title'] . "</label>\n";
                }
                switch ($value['form_type']){
                    case 0:
                        $html .= "<div class=\"layui-input-block\" >";
                        if($value['fill_flag'] == 0){
                            $html .= "<input type=\"text\" name=\"temp[{$value['id']}]\" placeholder=\"请输入\" autocomplete=\"off\" class=\"layui-input\">";
                        }else {
                            $html .= "<input type=\"text\" name=\"temp[{$value['id']}]\" lay-verify=\"required\" placeholder=\"请输入\" autocomplete=\"off\" class=\"layui-input\">";
                        }
                        $html .= "   </div>";;
                        break;
                    case 1:
                        $html .= "<div class=\"layui-input-block\" >";
                        if($value['fill_flag'] == 0){
                            $html .= "<textarea placeholder=\"请输入内容\" name='temp[{$value['id']}]' class=\"layui-textarea\"></textarea>";
                        }else {
                            $html .= "<textarea placeholder=\"请输入内容\" name='temp[{$value['id']}]' lay-verify=\"required\" class=\"layui-textarea\"></textarea>";
                        }
                        $html .= "   </div>";;
                        break;
                    case 2:
                        $html .= "<div class=\"layui-input-block\" >";
                        if($value['fill_flag'] == 0){
                            $html .= "<input type=\"number\" name=\"temp[{$value['id']}]\" placeholder=\"请输入\" autocomplete=\"off\" class=\"layui-input\">";
                        }else {
                            $html .= "<input type=\"number\" name=\"temp[{$value['id']}]\" lay-verify=\"required\" placeholder=\"请输入\" autocomplete=\"off\" class=\"layui-input\">";
                        }
                        $html .= "   </div>";;
                        break;
                    case 3:
                        $html .= "<div class=\"layui-input-block\" >";
                        if($value['fill_flag'] == 0){
                            $html .= "<input type=\"text\" name=\"temp[{$value['id']}]\" placeholder=\"请输入\" autocomplete=\"off\" class=\"layui-input\">";
                        }else {
                            $html .= "<input type=\"text\" name=\"temp[{$value['id']}]\" lay-verify=\"required\" placeholder=\"请输入\" autocomplete=\"off\" class=\"layui-input\">";
                        }
                        $html .= "   </div>";;
                        break;
                    case 4:
                        $html .= "<div class=\"layui-input-block\" >";
                        $html .= "<div class=\"layui-input-inline\" style='margin-top:10px;width:80px;' >";
                        if($value['fill_flag'] == 0){
                            $html .= "<input type=\"number\" name=\"temp[{$value['id']}][time][0]\" id='time_{$value['id']}' placeholder=\"请输入\" autocomplete=\"off\" class=\"layui-input\">"
                                . "</div>\n"
                                . "<div class=\"layui-form-mid\" style='margin-top:10px;'>:</div>"
                                . "<div class=\"layui-input-inline\" style='margin-top:10px;width:80px;' >"
                                . "<input type=\"number\" name=\"temp[{$value['id']}][time][1]\" placeholder=\"请输入\" autocomplete=\"off\" class=\"layui-input\" />"
                                . "</div>"
                                . "<div class=\"layui-form-mid\" style='margin-top:10px;'>-</div>"
                                . "<div class=\"layui-input-inline\" style='margin-top:10px;width:80px;'>"
                                . "<input type=\"number\" name=\"temp[{$value['id']}][time][2]\" placeholder=\"\" autocomplete=\"off\" class=\"layui-input\" />"
                                . "</div>"
                                . "<div class=\"layui-form-mid\" style='margin-top:10px;'>:</div>"
                                . "<div class=\"layui-input-inline\" style='margin-top:10px;width:80px;' >"
                                . "<input type=\"number\" name=\"temp[{$value['id']}][time][3]\" placeholder=\"\" autocomplete=\"off\" class=\"layui-input\" />"
                                . "</div>";
                        }else {
                            $html .= "<input type=\"number\" name=\"temp[{$value['id']}][time][0]\" id='time_{$value['id']}' lay-verify=\"required\" placeholder=\"请输入\" autocomplete=\"off\" class=\"layui-input\">"
                                . "</div>\n"
                                . "<div class=\"layui-form-mid\" style='margin-top:10px;' >:</div>\n"
                                . "<div class=\"layui-input-inline\" style='margin-top:10px;width:80px;' >\n"
                                . "<input type=\"number\" name=\"temp[{$value['id']}][time][1]\" placeholder=\"请输入\" autocomplete=\"off\" class=\"layui-input\" />\n"
                                . "</div>\n"
                                . "<div class=\"layui-form-mid\" style='margin-top:10px;' >-</div>\n"
                                . "<div class=\"layui-input-inline\" style='margin-top:10px;width:80px;' >\n"
                                . "<input type=\"number\" name=\"temp[{$value['id']}][time][2]\" placeholder=\"请输入\" placeholder=\"\" autocomplete=\"off\" class=\"layui-input\" />\n"
                                . "</div>\n"
                                . "<div class=\"layui-form-mid\" style='margin-top:10px;' >:</div>\n"
                                . "<div class=\"layui-input-inline\" style='margin-top:10px;width:80px;' >\n"
                                . "<input type=\"number\" name=\"temp[{$value['id']}][time][3]\" placeholder=\"请输入\" placeholder=\"\" autocomplete=\"off\" class=\"layui-input\" />\n"
                                . "</div>";
                        }
                        $html .= "</div>";
                        $html .= "   </div>";
                        break;
                    case 5:
                        $html .= "<div class=\"layui-input-block\" >";
                        $html .= "<div class=\"layui-input-inline\" style='' >";
                        $html .= "<select name=\"currency_type\">";
                        $html .= "<option value=\"currency_type\">请选择</option>";
                        $DataDicInfo = $DataDic->where(['data_type_no'=>'RESOURCE_CURRENCY', 'status'=>1])->field('data_no,data_name,data_icon')->select();
                        foreach ($DataDicInfo as $k2 => $v2){
                            $html .= "<option value=\"{$v2['data_no']}\">{$v2['data_name']}</option>";
                        }
                        $html .= "</select>";
                        $html .= "</div>";
                        $html .= "<div class=\"layui-input-inline\" style='' >";
                        if($value['fill_flag'] == 0){
                            $html .= "<input type=\"number\" name=\"temp[{$value['id']}]\" placeholder=\"请输入\" autocomplete=\"off\" class=\"layui-input\">";
                        }else {
                            $html .= "<input type=\"number\" name=\"temp[{$value['id']}]\" lay-verify=\"required\" placeholder=\"请输入\" autocomplete=\"off\" class=\"layui-input\">";
                        }
                        $html .= "</div>";
                        $html .= "</div>";
//                        $html .= "</div>";
//                        $html .= "   </div>";
                        break;
                    case 6:
                        /*$html .= "<div class=\"layui-input-block\" >";
                        $container = true;
                        $html .= "<script id=\"container\" name=\"intro\" type=\"text/plain\"></script>";
                        $html .= "   </div>";*/
                        break;
                    case 7:
                        /*$html .= '<button type="button" class="layui-btn" id="test7submit">产品图片</button>'
                            . '<blockquote class="layui-elem-quote layui-quote-nm" style="margin: 10px;">'
                            . '预览图：'
                            . '<div class="layui-upload-list" id="demo7"></div>'
                            . '<div style="clear:both;"></div>'
                            . '</blockquote>';*/
                        break;
                    case 8:
                        $html .= '<button type="button" class="layui-btn" id="test8submit">logo</button>'
                            . '<input type="hidden" name="logo" />'
                            . '<blockquote class="layui-elem-quote layui-quote-nm" style="margin: 10px;">'
                            . '预览图：'
                            . '<div class="layui-upload-list" id="demo8"></div>'
                            . '<div style="clear:both;"></div>'
                            . '</blockquote>';
                        break;
                    default:
                        break;
                }
                $html .= "   </div>";
            }
            return success_json('成功', ['html'=>$html,'container'=>$container]);
        } else {
            return error_json('沒有模板');
        }
        exit();
    }

    public function industry_subdivide()
    {
        $DataDic = model('DataDic');
        $data_top_id = \request()->param('data_top_id');
        $subdivide = $DataDic->where(['data_type_no'=>'RESOURCE_INDUSTRY_SUBDIVIDE', 'status'=>1,'data_top_id'=>$data_top_id])->select();
        return success_json('返回成功', ['sub'=>$subdivide]);
    }

    public function examine()
    {
        $Resource = model('Resource');
        $id = $this->request->param('id');
        $data = $Resource->where(['id'=>$id])->find();
        if(\request()->isPost()){
            $auth = \request()->post('auth');
            $feedback = \request()->post('feedback');

            $state = $Resource->save(['auth' => $auth, 'feedback' => $feedback], ['id' => $id]);
            if($state !== false) {
                $userInfo = model('UserInfo')->where(['uid' => $data['uid']])->find();
                $UserRechargeFind = model('UserRecharge')->find($userInfo['user_recharge_id']);
                if ($data['auth'] != $auth && ($auth == 1 || $auth == 2) && $data['ty'] == 1 && ($data['auth'] == 3 || $data['auth'] == 4 || $data['auth'] == 5)) {
                    // 加
                    model('UserRecharge')->where(['id' => $userInfo['user_recharge_id']])->setInc('used_publish');
                    model('PackageLog')->save([
                        'uid'         => $data['uid'],
                        'type'        => 1,
                        'recharge_id' => $userInfo['user_recharge_id'],
                        'package_id'  => $UserRechargeFind['package_id'],
                        'resource_id' => $id,
                        'remarks'     => '编辑',
                        'state'       => 1,
                    ]);

                } else if ($auth != $data['auth'] && ($auth == 3 || $auth == 4 || $auth == 5) && $data['ty'] == 1) {
                    // 减
                    model('UserRecharge')->where(['id' => $userInfo['user_recharge_id']])->setDec('used_publish');
                    model('PackageLog')->save([
                        'uid'         => $data['uid'],
                        'type'        => 1,
                        'recharge_id' => $userInfo['user_recharge_id'],
                        'package_id'  => $UserRechargeFind['package_id'],
                        'resource_id' => $id,
                        'remarks'     => '编辑',
                        'state'       => 2,
                    ]);
                }
                if( ($data['auth'] == 1 || $data['auth'] == 2) && $data['ty'] == 2){
                    model('card')->isUpdate(true)->save(['quality'=>1], ['uid'=>$data['uid']]);
                }
                return success_json(lang('EditSuccess', [lang('Resource')]));
            }
            return error_json(lang('EditFail', [lang('Resource')]));
        }

        $data['region'] = $data['region'] == '|' ? $data['region'] : explode('|', $data['region']);
        $DataDic = model('DataDic');
        if(is_array($data['region'])){
            $region = [];
            foreach ($data['region'] as $val){
                $ResourcesType = $DataDic->field('id,data_name')->where(['data_type_no' => 'RESOURCES_REGION', 'data_no' => $val, 'status' => 1])->find();
                array_push($region , $ResourcesType['data_name']);
            }
            $data['region'] = $region;
        }else{
            $data['region'] = $data['region'] == '|' ? array('不限') : array();
        }

        $type = explode('|', $data['type']);
        $valueType = [];
        foreach ($type as $val) {
            if (is_numeric($val)) {
                $ValuesType = $DataDic->field('id,data_name')->where(['data_type_no' => 'RESOURCES_TYPE', 'data_no' => $val, 'status' => 1])->find();
                array_push($valueType, !empty($ValuesType) ? $ValuesType['data_name'] : '');
            }
        }
        $data['type'] = $valueType;
        $ResourcesSu = $DataDic->field('id,data_name')->where(['data_type_no' => 'RESOURCES_SUBDIVIDE', 'data_no' => $data['business_subdivide'], 'status' => 1])->find();
        if(!empty($ResourcesSu)){
            $data['business_subdivide'] = $ResourcesSu['data_name'];

            //
            if($data['industry'] == '|'){
                $data['industry'] = '不限';
            }else {
                $industry = $DataDic->where(['data_type_no' => 'RESOURCE_INDUSTRY', 'status' => 1, 'data_top_id' => $ResourcesSu['id'], 'data_no'=>$data['industry']])->field('data_type_no,data_type_name,data_no,data_name')->find();
                if(!empty($industry)) {
                    $data['industry'] = isset($industry['data_name']) ? $industry['data_name'] : '';

                    $industry_subdivide = explode('|', $data['industry_subdivide']);
                    $subdivideAll = array();
                    foreach ($industry_subdivide as $key => $val2) {
                        $subdivide = $DataDic->where(['data_type_no' => 'RESOURCE_INDUSTRY_SUBDIVIDE', 'status' => 1, 'data_top_id'=>$industry['data_no'], 'data_no'=>$val2])->find();
                        array_push($subdivideAll,  isset($subdivide['data_name']) ? $subdivide['data_name'] : '' );
                    }
                    $data['industry_subdivide'] = $subdivideAll;
                }
            }
        }
        $resourceForm = model('resourceForm');
        $resourceFormTemplate = model('resourceFormTemplate');
        $resourceFromAll = $resourceForm->where(['resource_id' => $data['id'], 'status'=>1])->field('form_template_id,currency_type,content')->select();

        foreach ($resourceFromAll as $key => $val3) {
            $formTemplate = $resourceFormTemplate->where(['id'=>$val3['form_template_id']])->field('type,business_subdivide,ty,form_type,form_title,fill_flag')->find();
            $c = array_merge($val3->toArray(), $formTemplate->toArray());
            if($c['form_type'] == 4 || $c['form_type'] == 7) {
                $c['content'] = explode('|', $c['content']);
            }
            $resourceFromAll[$key] = $c;
        }

        $data['resourceFromAll'] = $resourceFromAll;
        $data['img'] = explode('|', $data['img']);
        return view('', ['data' => $data]);
    }

    public function Card()
    {
        if(\request()->isPost()) {
            return success_json('');
        }
        $uid = \request()->param('uid');
        $UserModel = model('User');
        $data = model('Card')->alias('A')
            ->join($UserModel->getTable().' B', 'A.uid=B.id')
            ->field('A.*,B.username,B.nickname,B.card_open,B.telegram,B.chat_id')
            ->where(['A.uid'=>$uid,'A.status'=>1])->find();
        if($data){
            $data['business_subdivide'] = explode('|', $data['business_subdivide']);
            $data['industry'] = explode('|', $data['industry']);
            $data['region'] = explode('|', $data['region']);

            $data['business_tag'] = explode('|', $data['business_tag']);
            $DataDicData = model('DataDic')->where(['data_type_no'=>'CONTACT_TYPE','status'=>1])->order('sort desc')->select();
            $resources = model('DataDic')->where(['data_type_no'=>'RESOURCES_TYPE','status'=>1])->order('sort desc')->select();
            $CardContact = model('CardContact')->where(['card_id'=>$data['id'],'status'=>1])->select();
        } else {
            $DataDicData = array();
            $CardContact = array();
            $resources = array();
        }
        $RESOURCES_TYPE   = model('DataDic')->selectType(['data_type_no' => 'RESOURCES_TYPE', 'status' => 1]);
        $RESOURCES_REGION = model('DataDic')->selectType(['data_type_no' => 'RESOURCES_REGION', 'status' => 1]);
        $ADVERT_ATTRIBUTE = model('DataDic')->selectType(['data_type_no' => 'ADVERT_ATTRIBUTE', 'status' => 1]);
        $FIRM_SCALE       = model('DataDic')->selectType(['data_type_no' => 'FIRM_SCALE', 'status' => 1]);
        return view('', [
            'data'=>$data,
            'DataDicData' => $DataDicData,
            'CardContact' => $CardContact,
            'resources' => $resources,
            'RESOURCES_TYPE'   => $RESOURCES_TYPE,
            'RESOURCES_REGION' => $RESOURCES_REGION,
            'ADVERT_ATTRIBUTE' => $ADVERT_ATTRIBUTE,
            'FIRM_SCALE'       => $FIRM_SCALE,
        ]);
    }
}
