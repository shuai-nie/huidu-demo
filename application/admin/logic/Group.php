<?php
namespace app\Admin\logic;
use think\Loader;
/**
 * logic
 */
class Group
{
	public function getAllGroup(){
		return Loader::model("Group")->select();
	}
	public function getPageWithAdmin($page,$limit){
		$Group=Loader::model("Group");
		$count=$Group->count();
		$list=$Group->limit(($page-1)*$limit.",$limit")->select();
		// dump($list);
		echo success_callback('',['count'=>$count,'list'=>$list]);
	}
	public function get_find($id){
		return Loader::model("Group")->find($id);
	}
	public function save_one($data){
		$data['rules']=implode(',',$data['rules']);
		Loader::model("Group")->where(array("id"=>input('get.id')))->update($data)===false ? error_callback("保存失败") : success_callback("保存成功");
	}
	public function insert_one($data){
		$data['rules']=implode(',',$data['rules']);
		Loader::model("Group")->insert($data)===false ? error_callback("保存失败") : success_callback("保存成功");
	}
	public function delete($id){
		Loader::model("Group")->where(array("id"=>$id))->delete()===false ? error_callback("删除失败") : success_callback("删除成功");
	}
}