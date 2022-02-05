<?php
namespace app\admin\model;
 
use think\Model;
 
class Admin extends Model{
	// 分组信息
	public function group(){
		return $this->hasOne('Group','id','group_id');
	}
}
