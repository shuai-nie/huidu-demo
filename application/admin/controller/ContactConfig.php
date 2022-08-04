<?php
namespace app\admin\controller;

use think\Db;

class ContactConfig extends Base
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        if (request()->isPost()) {
            $page  = request()->param('page');
            $limit = request()->param('limit');
            $offset = ($page - 1) * $limit ;
            $map   = ['type'=>1, 'status'=>1];
            $list  = model('Config')->where($map)->limit($offset, $limit)->order('id desc')->select();
            $count = model('Config')->where($map)->count();
            return json(['data' => ['count' => $count, 'list' => $list]], 200);
        }
        return view('', [
            'meta_title' => '联系方式配置',
        ]);
    }

    public function add()
    {
        $config = model('Config');
        if(request()->isPost()){
            $_post = request()->post();
            $count = Db::query("select auto_increment from information_schema.tables where table_schema='".config('database.database')."' and table_name='".$config->getTable()."'");
            $state = $config->data([
                'key' => $count[0]['AUTO_INCREMENT'] + 1 . "_xlfs",
                'value' => $_post['value'],
                'type' => 1,
                'remarks' => $_post['remarks'],
                'status' => 1,
                'create_time' => time(),
                'update_time' => time(),
            ])->save();
            if($state !== false) {
                return success_json("提交成功");
            }
            return error_json("提交失败");
        }
        return view('', []);
    }

    public function edit()
    {
        $config = model('Config');
        $id = request()->param('id');
        if(request()->isPost()) {
            $_post = request()->post();
            $state = $config->isUpdate(true)->save($_post, ['id'=>$id]);
            if($state !== false) {
                return success_json("提交成功");
            }
            return error_json("提交失败");
        }
        $info = $config->where(['id'=>$id])->find();
        return view('', [
            'info' => $info,
        ]);
    }

    public function delete()
    {
        $config = model('Config');
        $id = request()->param('id');
        $state = $config->isUpdate(true)->save(['status'=>0], ['id'=>$id]);
        if($state !== false) {
            return success_json("删除成功");
        }
        return error_json("删除失败");
    }

}