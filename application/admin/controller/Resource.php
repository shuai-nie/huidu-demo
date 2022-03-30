<?php

namespace app\admin\controller;

use think\Config;
use think\Controller;
use think\Request;

class Resource extends Base
{
    protected $ty = [
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
        $DataDic = model('DataDic');
        if (\request()->isPost()) {
            $map      = ['A.status' => 1];
            $page     = \request()->post('page');
            $limit    = \request()->post('limit', Config::get('paginate')['list_rows']);
            $offset   = ($page - 1) * $limit;
            $uid      = \request()->post('uid');
            $username = \request()->post('username');
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
            $data  = model("Resource")->alias('A')
                ->join(model('User')->getTable() . " B", "A.uid=B.id", 'left')
                ->where($map)->field('A.*,B.username')
                ->order('A.id desc')->limit($offset, $limit)->select();
            $count = model("Resource")->alias('A')
                ->join(model('User')->getTable() . " B", "A.uid=B.id", 'left')
                ->where($map)->count();

            foreach ($data as $key => $value) {
                $type      = explode('|', $value['type']);
                $valueType = [];
                foreach ($type as $val) {
                    if (is_numeric($val)) {
                        $ValuesType = $DataDic->field('id,data_name')->where(['data_type_no' => 'RESOURCES_TYPE', 'data_no' => $val])->find();
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
                            $ResourcesType = $DataDic->field('id,data_name')->where(['data_type_no' => 'RESOURCES_REGION', 'data_no' => $val])->find();
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
                            $ResourcesSu = $DataDic->field('id,data_name')->where(['data_type_no' => 'RESOURCES_SUBDIVIDE', 'data_no' => $val])->find();
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
            'ty'         => $this->ty,
            'type'       => $type,
            'meta_title' => '资源管理'
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
            $_post                       = request()->post();
            $_post['img']                = isset($_post['img']) ? implode('|', $_post['img']) : '';
            $_post['region']             = isset($_post['region']) ? implode('|', $_post['region']) : '';
            $_post['business_subdivide'] = isset($_post['subdivide']) ? implode('|', $_post['subdivide']) : '';
            $_post['top_start_time']     = !empty($_post['top_start_time']) ? strtotime($_post['top_start_time']) : 0;
            $_post['top_end_time']       = !empty($_post['top_end_time']) ? strtotime($_post['top_end_time']) : 0;
            unset($_post['subdivide']);
            $_post['types']      = 2;
            $_post['flush_time'] = time();
            $_post['intro']      = htmlspecialchars_decode($_post['intro']);
            if (($_post['auth'] == 1 || $_post['auth'] == 2) && $_post['ty'] == 1) {
                $this->userpublish($_post['uid']);
            }
            $state = model('Resource')->save($_post);
            if ($state !== false) {
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
                        'resource_id' => model('Resource')->id,
                        'remarks'     => '新建',
                        'state'       => 1,
                    ]);
                }
                return success_json(lang('CreateSuccess', [lang('Resource')]));
            }
            return error_json(lang('CreateFail', [lang('Resource')]));
        }
        $DataDic         = model('DataDic');
        $resourcesType   = $DataDic->where(['data_type_no' => 'RESOURCES_TYPE', 'status' => '1'])->order('sort desc')->select();
        $resourcesRegion = $DataDic->where(['data_type_no' => 'RESOURCES_REGION', 'status' => '1'])->order('sort desc')->select();
        $DataDicData     = $DataDic->where(['data_type_no' => 'CONTACT_TYPE', 'status' => 1])->order('sort desc')->select();
        $Subivde         = $DataDic->where(['data_type_no' => 'RESOURCES_SUBDIVIDE', 'status' => 1, 'data_top_id' => $resourcesType[0]['data_no']])->order('sort desc')->select();
        return view('', [
            'resourcesType'   => $resourcesType,
            'resourcesRegion' => $resourcesRegion,
            'DataDicData'     => $DataDicData,
            'ty'              => $this->ty,
            'Subivde'         => $Subivde
        ]);
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
            $_post                       = request()->post();
            $_post['img']                = isset($_post['img']) ? implode('|', $_post['img']) : '';
            $_post['region']             = isset($_post['region']) ? implode('|', $_post['region']) : '';
            $_post['business_subdivide'] = isset($_post['subdivide']) ? implode('|', $_post['subdivide']) : '';
            $_post['top_start_time']     = !empty($_post['top_start_time']) ? strtotime($_post['top_start_time']) : 0;
            $_post['top_end_time']       = !empty($_post['top_end_time']) ? strtotime($_post['top_end_time']) : 0;
            $_post['intro']              = htmlspecialchars_decode($_post['intro']);
            if ($resourceInfo['auth'] != $_post['auth'] && ($_post['auth'] == 1 || $_post['auth'] == 2) && $_post['ty'] == 1 && ($resourceInfo['auth'] == 3 || $resourceInfo['auth'] == 4 || $resourceInfo['auth'] == 5)) {
                $this->userpublish($_post['uid']);
            }

            $state = $Resource->save($_post, ['id' => $id]);
            if ($state !== false) {
                $userInfo         = model('UserInfo')->where(['uid' => $_post['uid']])->find();
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
                return success_json(lang('EditSuccess', [lang('Resource')]));
            }
            return error_json(lang('EditFail', [lang('Resource')]));
        }

        $DataDic             = model('DataDic');
        $resourcesType       = $DataDic->where(['data_type_no' => 'RESOURCES_TYPE', 'status' => 1])->select();
        $resourcesRegion     = $DataDic->where(['data_type_no' => 'RESOURCES_REGION', 'status' => 1])->select();
        $resourceInfo['img'] = explode('|', $resourceInfo['img']);
        if ($resourceInfo['region'] == '|') {
            $resourceInfo['region'] = array('|');
        } else {
            $resourceInfo['region'] = explode('|', $resourceInfo['region']);
        }
        $resourceInfo['business_subdivide'] = explode('|', $resourceInfo['business_subdivide']);
        $resourceInfo['top_start_time']     = $resourceInfo['top_start_time'] > 10000 ? date('Y-m-d H:i:s', $resourceInfo['top_start_time']) : '';
        $resourceInfo['top_end_time']       = $resourceInfo['top_end_time'] > 10000 ? date('Y-m-d H:i:s', $resourceInfo['top_end_time']) : '';

        $DataDicData = $DataDic->where(['data_type_no' => 'CONTACT_TYPE', 'status' => 1])->order('sort desc')->select();
        $Subivde     = $DataDic->where(['data_type_no' => 'RESOURCES_SUBDIVIDE', 'status' => 1, 'data_top_id' => $resourceInfo['type']])->order('sort desc')->select();


        if ($resourceInfo['business_subdivide'][0]) {
            $RESOURCES   = $DataDic->where(['data_type_no' => 'RESOURCES_SUBDIVIDE', 'status' => 1])->find();
            $data_top_id = $RESOURCES['data_top_id'];
        } else {
            $data_top_id = $Subivde[0]['data_no'];
        }

        return view('', [
            'resource'        => $resourceInfo,
            'resourcesType'   => $resourcesType,
            'resourcesRegion' => $resourcesRegion,
            'DataDicData'     => $DataDicData,
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
                if ($resourceInfo['auth'] == 1 || $resourceInfo['auth'] == 2) {
                    $userInfo         = model('UserInfo')->where(['uid' => $resourceInfo['uid']])->find();
                    $UserRechargeFind = model('UserRecharge')->find($userInfo['user_recharge_id']);
                    // 加
                    model('UserRecharge')->where(['id' => $userInfo['user_recharge_id']])->setInc('used_publish');
                    model('PackageLog')->save([
                        'uid'         => $resourceInfo['uid'],
                        'type'        => 1,
                        'recharge_id' => $userInfo['user_recharge_id'],
                        'package_id'  => $UserRechargeFind['package_id'],
                        'resource_id' => $id,
                        'remarks'     => '删除',
                        'state'       => 1,
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
        return view();
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
}
