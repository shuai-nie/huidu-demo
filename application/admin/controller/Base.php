<?php
namespace app\admin\controller;
use think\Controller;
use think\Loader;
use app\admin\logic\Common;

class Base extends Controller
{

	public function _initialize()
    {
    	if(empty(getLoginUserId())){
    		$this->redirect("/Admin/Login");
    	}
    }
}