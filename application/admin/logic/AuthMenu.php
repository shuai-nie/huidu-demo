<?php
namespace app\Admin\logic;
use think\Loader;
/**
 * logic
 */
class AuthMenu
{
	/*
	获取所有用户--分页
	*/
	function getPageWithAdmin($page,$limit){
		$AuthMenu=Loader::model("AuthMenu");
		$count=$AuthMenu->count();
		$list=$AuthMenu->limit(($page-1)*$limit.",$limit")->select();
		// dump($list);
		echo success_callback('',['count'=>$count,'list'=>$list]);
	}
	function get_find($id){
		return Loader::model("AuthMenu")->find($id);
	}
	function get_all_menu($map=[]){
		return Loader::model("AuthMenu")->where($map)->select();
	}
	function save_one($data){
		return Loader::model("AuthMenu")->where(array("id"=>input('get.id')))->update($data)===false ? error_callback("失败") : success_callback("成功");
	}
	function add_one($data){
		return Loader::model("AuthMenu")->insert($data)===false ? error_callback("失败") : success_callback("成功");
	}
	public function delete($id){
		Loader::model("AuthMenu")->where(array("id"=>$id))->delete()===false ? error_callback("删除失败") : success_callback("删除成功");
	}
}