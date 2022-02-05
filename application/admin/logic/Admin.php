<?php
namespace app\Admin\logic;
use think\Loader;
/**
 * logic
 */
class Admin
{
	/*
	获取所有用户--分页
	*/
	function getPageWithAdmin($page,$limit){
		$Admin=Loader::model("Admin");
		$count=$Admin->count();
		$list=$Admin->with('group')->limit(($page-1)*$limit.",$limit")->select();
		// dump($list);
		echo success_callback('',['count'=>$count,'list'=>$list]);
	}
	// 获取一条
	function get_find($id){
		return Loader::model("Admin")->with('group')->find($id);
	}
	// 修改保存
	function save_one($data){
		 Loader::model("Admin")->where(array("id"=>input('get.id')))->update($data)===false ? error_callback("失败了") : success_callback("保存成功");
	}
	function add_one($data){
		Loader::model("Admin")->insert($data)===false ? error_callback("保存失败"):success_callback("保存成功");
	}
	function delete($id){
		Loader::model("Admin")->where(array("id"=>$id))->delete()===false ? error_callback("删除失败") :
		success_callback("删除成功");
	}
}