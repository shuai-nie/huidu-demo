<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

class Coop extends Base
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        if(Request()->isPost()) {
            $map = ['A.status'=>1];
            $page = Request()->post('page');
            $limit = Request()->post('limit');
            $offset = ($page - 1) * $limit;
            $uname = \request()->post('uname');
            $type = \request()->post('type');
            $type_status = \request()->post('type_status');
            if(!empty($uname)) {
                $map['A.uname'] = ['like', "%{$uname}%"];
            }
            if(!empty($type)){
                $map['A.type'] = $type;
            }
            if(!empty($type_status)){
                $map['A.type_status'] = $type_status;
            }

            $data = model("Cooperation")->alias('A')
                ->join(model('Resource')->getTable()." B", "A.rid=B.id", "left")
                ->field("A.*,B.title")
                ->where($map)->limit($offset, $limit)->order('A.id desc')->select();
            $count = model("Cooperation")->alias('A')
                ->join(model('Resource')->getTable()." B", "A.rid=B.id", "left")
                ->where($map)->count();
            foreach ($data as $k => $v) {
                if (!empty( $v['uid'])) {
                    $CacheUser = CacheUser($v['uid']);
                    $v['u_username'] = $CacheUser['username'];
                }
                if (!empty( $v['uid'])) {
                    $CacheUser = CacheUser($v['fuid']);
                    $v['f_username'] = $CacheUser['username'];
                }
                $data[$k] = $v;
            }
            return json(['data'=>['count'=>$count, 'list'=>$data]], 200);
        }
        return view('', [
            'meta_title' => '合作动态',
        ]);
    }

    /**
     * 显示创建资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        $userModel = model('user');
        if(Request()->isPost()) {
            $data = Request()->post();
            $userInfo = $userModel->where(['id'=>$data['uid']])->find();
            $fuserInfo = $userModel->where(['id'=>$data['fuid']])->find();
            $data['uname'] = $userInfo['username'];
            $data['username'] = $fuserInfo['username'];
            $state = model("Cooperation")->save($data);
            if($state !== false){
                return success_json(lang('CreateSuccess', [lang('COOPERATION')]));
            }
            return error_json(lang('CreateFail', [lang('COOPERATION')]));
        }
        $user = model('user')->where(['status'=>1])->field('id,username')->select();
        $resource = model('resource')->where(['status'=>1,'auth'=>1])->field('id,title')->select();
        return view('', [
            'user' => $user,
            'resource' => $resource,
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
        $userModel = model('user');
        if(Request()->isPost()) {
            $data = Request()->post();
            $userInfo = $userModel->where(['id'=>$data['uid']])->find();
            $fuserInfo = $userModel->where(['id'=>$data['fuid']])->find();
            $data['uname'] = $userInfo['username'];
            $data['username'] = $fuserInfo['username'];
            $state = model("Cooperation")->save($data, ['id'=>$id]);
            if($state !== false){
                return success_json(lang('EditSuccess', [lang('COOPERATION')]) );
            }
            return error_json(lang('EditFail', [lang('COOPERATION')]));
        }
        $data = model("Cooperation")->find($id);
        $user = model('user')->where(['status'=>1])->field('id,username')->select();
        $resource = model('resource')->where(['status'=>1,'auth'=>1])->field('id,title')->select();
        return view('edit', [
            'data' => $data,
            'user' => $user,
            'resource' => $resource,
        ]);
    }

    /**
     * 删除指定资源
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        $id = Request()->param('id');
        $state = model("Cooperation")->save(['status'=>0], ['id'=>$id]);
        if($state !== false){
            return success_json(lang('DeleteSuccess', [lang('COOPERATION')]) );
        }
        return error_json(lang('DeleteFail', [lang('COOPERATION')]) );
    }
}
