<?php
namespace app\admin\controller;
use think\Loader;
use lib\Jurisdiction;
class Group extends Base
{
	private $model;
	private $logic;
	function _initialize(){
		parent::_initialize();
		$this->model=Loader::model("Group");
		$this->logic=Loader::model('Group','logic');
	}
	public function index(){
		if($this->page!=null){
			$this->logic->getPageWithAdmin($this->page,$this->limit);exit;
		}
		return view();
	}
	public function edit($id){
		if(request()->isPost()){
			$this->logic->save_one(input('post.'));
		}
		$Jurisdiction=new Jurisdiction();
        $menuList=$Jurisdiction->getAuthMenu(getLoginUserId(),0);
		$info=$this->logic->get_find($id);
		return view('',[
			'info'=>$info,
			'menuList'=>$menuList
		]);
	}

	public function add(){
		if(request()->isPost()){
			$this->logic->insert_one(input('post.'));
		}
		$Jurisdiction=new Jurisdiction();
        $menuList=$Jurisdiction->getAuthMenu(getLoginUserId(),0);
		return view('',[
			'menuList'=>$menuList
		]);
	}

    /**
     * @param object|Request|null $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    public function add2()
    {
        return view('add2');
    }

	public function delete($id=""){
		if($id!=""){
			$this->logic->delete($id);
		}
	}
}