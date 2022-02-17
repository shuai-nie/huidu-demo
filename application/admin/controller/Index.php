<?php
namespace app\admin\controller;
use lib\Jurisdiction;
use think\Loader;
use think\View;

class Index extends Base
{
    public function index()
    {
        $Jurisdiction = new Jurisdiction();
        $menuList = $Jurisdiction->getAuthMenu(getLoginUserId(), 1);
        $userInfo = Loader::model("Admin", 'logic')->get_find(getLoginUserId());
    	return view('', [
    		'menuList'=>$menuList,
    		'userInfo'=>$userInfo
    	]);
    }

    public function logout()
    {
    	if(request()->isPost()){
           setLoginUserId(null);
           success_callback("成功");
        }
	    setLoginUserId(null);
    }

	public function edit()
	{
		if(request()->isPost()){
			$old = input('password_old');
			$new = input('password_new');
			$admin = Loader::model('admin')->where(array('id'=>getLoginUserId()))->find();
			if($admin['password'] != md5(md5($old).$admin->str) ) error_callback('旧密码不正确');
			$succ = Loader::model('admin')->where(array('id'=>getLoginUserId()))->update(array('password'=>md5(md5($new).$admin->str)));
			if($succ !== false){
				return success_json();
			}
            return error_json();
		}
		return view();
	}
}