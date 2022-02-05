<?php

namespace app\Admin\logic;

use think\Loader;
use think\Collection;
/**
 * logic
 */
class Common
{
	public function get_list($model, $page, $limit, $where = array(), $order = 'id desc')
	{
		$l_model = Loader::model($model);
		$count = $l_model->where($where)->count();
		$list = $l_model->where($where)->limit(($page - 1) * $limit . ",$limit")->order($order)->select();
		// dump($list);
		echo success_callback('', ['count' => $count, 'list' => $list]);
	}
	
	public function getPageWithAdmin($page, $limit)
	{
		$Group = Loader::model("Group");
		$count = $Group->count();
		$list = $Group->limit(($page - 1) * $limit . ",$limit")->select();
		// dump($list);
		echo success_callback('', ['count' => $count, 'list' => $list]);
	}
	
	public function get_find($model, $id)
	{
		return Loader::model($model)->where(array('id' => $id))->find();
	}
	
	public function save_one($model, $data)
	{
		Loader::model($model)->where(array("id" => input('id')))->update($data) === false ? error_callback("保存失败") : success_callback("保存成功");
	}
	
	public function delete($model, $id)
	{
		Loader::model($model)->where(array("id" => $id))->delete() === false ? error_callback("删除失败") : success_callback("删除成功");
	}
	
	function add($model, $data)
	{
		Loader::model($model)->insert($data) === false ? error_callback("失败了") : success_callback("保存成功");
	}
	

	public function insert_one($model,$data){
		Loader::model($model)->insert($data)===false ? error_callback("保存失败") : success_callback("保存成功");
	}

	
	//删除首页轮播图片
	public function del_index_lunbo($imageArr)
	{
		if (!empty($imageArr)) {
			foreach ($imageArr as $k => $v) {
				if (file_exists(ROOT_PATH . '/public/' . $v)) {
					@unlink(ROOT_PATH . '/public/' . $v);
				}
			}
		}
		return 'success';
	}
	
	//更新年级的数量信息
	public function update_course()
	{
	
		
	}
	
}