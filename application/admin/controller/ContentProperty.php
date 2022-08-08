<?php
namespace app\admin\controller;

class ContentProperty extends Base
{
    public function _initialize()
    {
        $this->assign('meta_title', '文章属性');
        parent::_initialize();
    }

    public function index()
    {
        $ContentProperty = model('ContentProperty');
        if(request()->isPost()){
            $map = ['status'=>1];
            $name = \request()->post('name');
            if(!empty($name)) {
                $map['name'] = ['like', "%{$name}%"];
            }
            $data = $ContentProperty->where($map)->order('id desc')->select();
            $count = $ContentProperty->where($map)->count();
            return json(['data'=>['count'=>$count, 'list'=>$data]], 200);
        }
        return view('', []);
    }

    public function create()
    {
        $ContentProperty = model('ContentProperty');
        if(Request()->isPost()) {
            $data = Request()->param();
            $state = $ContentProperty->save($data);
            if($state !== false){
                return success_json("提交成功");
            }
            return error_json("提交失败");
        }
        return view();
    }

    public function edit($id)
    {
        $ContentProperty = model('ContentProperty');
        if(Request()->isPost()) {
            $data = Request()->param();
            $state = $ContentProperty->save($data, ['id'=>$data['id']]);
            if($state !== false){
                return success_json("提交成功");
            }
            return error_json("提交失败");
        }
        $data = $ContentProperty->find($id);
        return view('edit', ['data'=>$data]);
    }

    public function delete($id)
    {
        $ContentProperty = model('ContentProperty');
        $id = \request()->param('id');
        $state = $ContentProperty->save(['status'=>0], ['id'=>$id]);
        if($state !== false){
            return success_json("删除成功");
        }
        return error_json("删除失败");
    }



}