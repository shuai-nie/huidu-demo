<?php
namespace app\admin\controller;

class SearchKeyword extends Base
{
    public function index()
    {
        $SearchKeyword = model('SearchKeyword');
        if(request()->isPost()){

            $limit = request()->post('limit');
            $page = request()->post('page', 1);
            $type = request()->param('type');
            $name = request()->post('name');
            $offset = ($page - 1) * $limit;
            $map = ['status'=>1, 'type'=>$type];
            if(!empty($name)) {
                $map['name'] = ['like', "%{$name}%"];
            }
            $count = $SearchKeyword->where($map)->count();
            $data = $SearchKeyword->where($map)->order('id desc')->limit($offset, $limit)->select();
            $SearchKeywordExposure = model('SearchKeywordExposure');
            foreach ($data as $k => $v){
                $v['zong'] = $SearchKeywordExposure->where(['search_keyword_id' => $v['id']])->count();
                $data[$k] = $v;

            }
            return json(['data'=>['count'=>$count, 'list'=>$data]], 200);
        }
        getAdminLog("查看 搜索词推荐");
        return view('', [
            'meta_title' => '搜索词推荐',
            'type' => $SearchKeyword->type
        ]);
    }

    public function create()
    {
        $SearchKeyword = model('SearchKeyword');
        if(request()->isPost()) {
            $_post = request()->post();
            $_post['type'] = request()->param('type');
            $state = $SearchKeyword->isUpdate(false)->save($_post);
            if($state !== false) {
                getAdminLog("新建 热门词条管理 type". $_post['type']. " ID ".$SearchKeyword->id);
                return success_json('提交成功', ['type'=>$_post['type']]);
            }
            return error_json('提交失败');
        }
        return view('', []);
    }

    public function edit()
    {
        $SearchKeyword = model('SearchKeyword');
        $id = request()->param('id');
        $info = $SearchKeyword->where(['id' => $id])->find();
        if(request()->isPost()) {
            $_post = request()->post();
            $state = $SearchKeyword->isUpdate(true)->save($_post, ['id'=>$id]);
            if($state !== false) {
                getAdminLog("编辑 热门词条管理 type". $info['type']. " ID ".$id);
                return success_json('提交成功', ['type'=>$info['type']]);
            }
            return error_json('提交失败');
        }
        return view('', [
            'info' => $info
        ]);
    }

    public function delete()
    {
        if(request()->isPost()) {
            $SearchKeyword = model('SearchKeyword');
            $_post = request()->post();
            $info = $SearchKeyword->where(['id' => $_post['id']])->find();
            $state = $SearchKeyword->isUpdate(true)->save(['status' => 0], ['id' => $_post['id']]);
            if($state !== false) {
                getAdminLog("删除 热门词条管理 type". $info['type']. " ID ".$_post['id']);
                return success_json('提交成功', ['type'=>$info['type']]);
            }
            return error_json('提交失败');
        }

    }




}