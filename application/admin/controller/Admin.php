<?php
namespace app\admin\controller;
use think\Loader;
class Admin extends Base
{
	private $model;
	private $logic;
	function _initialize(){
		parent::_initialize();
		$this->model=Loader::model("Admin");
		$this->logic=Loader::model('Admin','logic');
	}
	// 主页
	public function index()
    {
        if (Request()->isPost()) {
			//$this->logic->getPageWithAdmin($this->page,$this->limit);exit;
            $map = [];
            $userData = model("Admin")->where($map)->select();
            $count = model("Admin")->where($map)->count();
            $data  = [
                'code' => 0,
                'msg'  => '',
                'data' => [
                    'count' => $count,
                    'list'  => $userData
                ],
            ];
            return json($data);

		}
		return view();
	}
	// 编辑
	public function edit(){
		if(request()->isPost()){
			$this->logic->save_one(input('post.'));
		}
		$AllGroup=Loader::model("Group",'logic')->getAllGroup();
		$info=$this->logic->get_find(input('get.id'));
		return view('',[
			'groupList'=>$AllGroup,
			'info'=>$info
		]);
	}
	/*
	新增
	*/
	function add(){
		if(request()->isPost()){
			$this->logic->add_one(input('post.'));
		}
		$AllGroup=Loader::model("Group",'logic')->getAllGroup();
		return view('',[
			'group'=>$AllGroup
		]);
	}
	/*删除*/
	function delete($id=""){
		if($id!=""){
			$this->logic->delete($id);
		}
	}
}