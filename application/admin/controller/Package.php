<?php

namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;

class Package extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        if(Request()->isPost()) {
            $map = ['status'=>1];
            $page = Request()->param('page');
            $limit = Request()->param('limit');
            $PackagePrice = model('PackagePrice');
            $Package = model("Package");
            $offset = ($page - 1) * $limit;
            $data = $Package->where($map)->limit($offset, $limit)->select();
            $count = $Package->where($map)->count();
            foreach ($data as $key=>$val) {
                $price = $PackagePrice->where(['package_id' => $val['id'], 'status' => 1])->select();
                if(!empty($price)) {
                    $str = '';
                    foreach ($price as $k=>$v) {
                        $str .= $v['old_amount'] .'/'. getPriceType($v['type']) .'('.$v['new_amount'].")<br/>";

                    }
                    $val['price'] = $str;
                }
                $data[$key] = $val;
            }
            return json(['data'=>['count'=>$count, 'list'=>$data]], 200);
        }
        return view();
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        if(\request()->isPost()){
            $_post = \request()->param();
            $packageHistory = model('packageHistory');
            Db::startTrans();
            $state = false;

            try {
                $packageHistory->saveId($_post);
                $_post['history_id'] = $packageHistory->id;
                model("Package")->saveId($_post);
                Db::commit();
                $state = true;
            } catch (\Exception $e){
                Db::rollback();;
                $state = false;
            }

            if($state !== false){
                return success_json(lang('CreateSuccess', [lang('RESOURCE')]));
            }
            return error_json(lang('CreateFail', [lang('RESOURCE')]));
        }
        return view('');
    }



    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        $Package = model("Package");
        if(\request()->isPost()){
            $_post = \request()->param();
            $packageHistory = model('packageHistory');
            $state = false;
            Db::startTrans();
            try {
                $save = $_post;
                 unset($save['id']);
                $packageHistory->saveId($save);
                $_post['history_id'] = $packageHistory->id;
                $Package->allowField(true)->isUpdate(true)->save($_post, ['id'=>$id]);

                Db::commit();
                $state = true;
            } catch (\Exception $e) {
                Db::rollback();
                $state = false;
            }

            if($state !== false){
                return success_json(lang('EditSuccess', [lang('RESOURCE')]));
            }
            return error_json(lang('EditFail', [lang('RESOURCE')]));
        }
        $data = $Package->find($id);
        return view('', ['data'=>$data]);
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $Package = model("Package");
        if($id != ''){
            $_post = \request()->param();
            $state = $Package->save(['status'=>0], ['id'=>$id]);
            if($state !== false){
                return success_json(lang('DeleteSuccess', [lang('RESOURCE')]));
            }
            return error_json(lang('DeleteFail', [lang('RESOURCE')]));
        }
    }

    public function price($id)
    {
        $PackagePrice = model('PackagePrice');
        if(\request()->isPost()) {
            $_post = \request()->post();
            $add = array();
            $update = array();
            foreach ($_post['old_amount'] as $key => $val){
                if(isset($_post['mid'][$key])){
                    array_push($update,  array(
                        'id' => $_post['mid'][$key],
                        'package_id' => $id,
                        'type' => $_post['type'][$key],
                        'old_amount' => $_post['old_amount'][$key],
                        'new_amount' => $_post['new_amount'][$key],
                        'sort' => $_post['sort'][$key],
                        'recommend' => isset($_post['recommend'][$key]) ? 1 : 0,
                        'status' => 1
                    ));
                } else {
                    array_push($add,  array(
                        'package_id' => $id,
                        'type' => $_post['type'][$key],
                        'old_amount' => $_post['old_amount'][$key],
                        'new_amount' => $_post['new_amount'][$key],
                        'sort' => $_post['sort'][$key],
                        'recommend' => isset($_post['recommend'][$key]) ? 1 : 0,
                    ));
                }
            }
            $state = false;
            $PackagePrice->save(['status' => 0], ['package_id' => $id]);
            if (!empty($add)) {
                $state = $PackagePrice->isUpdate(false)->saveAll($add, false);
            }

            if (!empty($update)) {
                $state = $PackagePrice->isUpdate(true)->saveAll($update);
            }

            if($state !== false) {
                return success_json('修改成功');
            }
            return error_json('修改失败');
        }
        $info = $PackagePrice->where(['package_id' => $id, 'status' => 1])->select();
        $count = $PackagePrice->where(['package_id' => $id, 'status' => 1])->count();
        return view('', [
            'id' => $id,
            'info' => $info,
            'count' => $count,
        ]);
    }
}
