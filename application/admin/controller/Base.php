<?php
namespace app\admin\controller;
use think\Controller;
use think\Loader;
use app\admin\logic\Common;

class Base extends Controller
{
	public $page;
	public $limit;
	public $common;
	public function _initialize()
    {
    	$this->common=new Common();
    	$this->page=null;
    	$this->limit=null;
    	if(empty(getLoginUserId())){
    		$this->redirect("/Admin/Login");
    	}
//    	if(!empty(getLoginStr()))
//	    {
//		    $is_exist = Loader::model('admin')->where(array('str'=>getLoginStr()))->find();
////		    dump($is_exist['str']);exit;
//		    if($is_exist == false)
//		    {
//			    setLoginUserId(null);
//			    $this->redirect("/Admin/Login");
//			    exit;
//		    }
//	    }
    	if(!empty(input('get.page'))){
    		$this->page=input('get.page');
    	}
    	if(!empty(input('get.limit'))){
    		$this->limit=input('get.limit');
    	}
        // echo json_encode(["msg"=>"食物"]);exit;
	    Loader::model('admin')->where(array('str'=>getLoginStr()))->find();
    }
}