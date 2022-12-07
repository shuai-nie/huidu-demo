<?php
namespace app\admin\controller;

use app\admin\model\NavigationBar;
use util\Tree;

class Navigation extends Base
{
    public function index()
    {
        if(request()->isPost()){
            $data = NavigationBar::where([])->order("sort desc, id desc")->select();
            return json(['code'=>0,'count'=>0,'data'=>$data], 200);
        }
        return view('', ['meta_title' => '导航栏配置']);
    }

    public function add()
    {
        if(request()->isPost()){
            $_post = request()->post();
            $state = NavigationBar::create($_post);
            if($state != false){
                return success_json('添加成功');
            }
            return error_json('添加失败');
        }
        return view('', ['link_type'=>NavigationBar::$link_type]);
    }

    public function json()
    {
        Tree::config([
            'id'    => 'id',
            'pid'   => 'pid',
            'title' => 'title',
            'child' => 'children',
            'html'  => '┝ ',
            'step'  => 4,
        ]);

        $data = NavigationBar::where([])->field('id,pid,title')->order("sort desc, id desc")->select();
        if($data) {
            $data = collection($data)->toArray();
        }
        foreach ($data as  $key => $value) {
            $value['name'] = $value['title'];
            $value['open'] = true;
            $data[$key] = $value;
        }
        $data = Tree::toLayer($data);
        array_unshift($data, ['id' => 0, 'pid' => 0, 'name' => '顶级']);
        return json($data, 200);
    }

}