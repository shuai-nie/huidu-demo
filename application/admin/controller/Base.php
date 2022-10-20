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
            setLoginUserId(null);
    		$this->redirect("/Admin/Login");
    	}else{
            $str = getLoginUserStr();
            $admin = model('Admin')->where(['id'=>getLoginUserId()])->find();
            if($admin['str'] != $str) {
                setLoginUserId(null);
                $this->redirect("/Admin/Login");
            }
        }
    }
}