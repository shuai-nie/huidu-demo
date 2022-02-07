<?php
namespace app\admin\model;
 
use think\Log;
use think\Model;
 
class AuthMenu extends Model{

    protected static function init()
    {
       AuthMenu::beforeUpdate(function($data){
           Log::log('AuthMenuAuthMenuAuthMenuAuthMenuAuthMenuAuthMenu');
       });
    }
}