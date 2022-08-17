<?php
namespace app\admin\controller;

class Channel extends Base
{

    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        $Channel = model('Channel');
        if(request()->isPost()){
            $limit = request()->post('limit');
            $page = request()->post('page', 1);
            $offset = ($page - 1) * $limit;
            $map = ['status'=>1];
            $count = $Channel->where($map)->count();
            $list = $Channel->where($map)->order('id desc')->limit($offset, $limit)->select();
            foreach ($list as $k=>$v){
                $v['key'] = $k+ ($page-1)*$limit+1;
                $list[$k] = $v;
            }
            return json(['data'=>['count'=>$count, 'list'=>$list]], 200);
        }
        return view('', [
        ]);
    }

    public function create()
    {
        $Channel = model('Channel');
        if(request()->isPost()) {
            $_post = request()->post();
            $count = $Channel->where(['channel_key'=>$_post['channel_key']])->count();
            if($count > 0){
                return error_json("渠道标识 已存在");
            }
            $state = $Channel->allowField(true)->data($_post)->save();
            if($state != false) {
                return success_json("提交成功");
            }
            return error_json("提交失败");
        }

        return view('', [
        ]);
    }

    public function edit()
    {
        $Channel = model('Channel');
        $id = request()->param('id');
        if(request()->isPost()) {
            $_post = request()->post();
            $count = $Channel->where(['channel_key'=>$_post['channel_key'], 'id'=>['neq', $id]])->count();
            if($count > 0){
                return error_json("渠道标识 已存在");
            }

            $state = $Channel->allowField(true)->isUpdate(true)->save($_post, ['id'=>$id]);
            if($state != false) {
                return success_json("提交成功");
            }
            return error_json("提交失败");
        }

        $info = $Channel->where(['id'=>$id])->find();
        return view('', [
            'info' => $info,
        ]);
    }

    public function delete()
    {
        $Channel = model('Channel');
        $id = request()->param('id');
        $state = $Channel->isUpdate(true)->save(['status'=>0], ['id'=>$id]);
        if ($state != false) {
            return success_json("刪除成功");
        }
        return error_json("删除失败");
    }

}