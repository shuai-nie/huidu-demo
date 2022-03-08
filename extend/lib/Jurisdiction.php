<?php

namespace lib;

use think\Controller;
use think\Config;

/**
 *
 */
class Jurisdiction extends Controller
{
    private $config = [
        'jump'     => ['1'],
        'authMenu' => "auth_menu",
        'authUser' => 'admin',
        'group'    => 'group',
    ];

    // 构造函数
    public function __construct()
    {
        //可设置配置项 auth, 此配置项为数组。
        if ($auth = Config::get('auth')) {
            $this->config = array_merge($this->config, $auth);
        }
        // 初始化request

    }

    /*
        获取权限菜单
    */
    public function getAuthMenu($uid = "", $show = 0)
    {
        $addAuthMenu = $this->getrule($uid, $show);
        //提取数组id
        $keys = array_column($addAuthMenu, 'id');
        //将数组id作为key
        $menu     = array_combine($keys, $addAuthMenu);
        $pids     = [];
        $menuList = [];
        foreach ($menu as $key => $value) {
            if ($value['pid'] != 0) {
                if (!in_array($value['pid'], $pids)) {
                    $menuList[] = $menu[$value['pid']];
                    $pids[]     = $value['pid'];
                }
            }
            $menuList[] = $value;
        }
        return $this->channelLevel($menuList);
    }

    /*序列化菜单*/
    public function channelLevel($data, $pid = 0, $html = "&nbsp;", $fieldPri = 'id', $fieldPid = 'pid', $level = 1)
    {
        if (empty($data)) {
            return array();
        }
        $arr = array();
        foreach ($data as $v) {
            if ($v[$fieldPid] == $pid) {
                $arr[$v[$fieldPri]]           = $v;
                $arr[$v[$fieldPri]]['_level'] = $level;
                $arr[$v[$fieldPri]]['_html']  = str_repeat($html, $level - 1);
                $arr[$v[$fieldPri]]["_data"]  = self::channelLevel($data, $v[$fieldPri], $html, $fieldPri, $fieldPid, $level + 1);
            }
        }
        return $arr;
    }

    // 获取用户权限数据库
    public function getrule($uid = "", $show = 0)
    {
        // 如果是超级用户
        $map = $addAuthMenu = [];
        if ($show == 1) {
            $map['show'] = 1;
        }

        if (in_array($uid, $this->config["jump"])) {
            $addAuthMenu = db($this->config["authMenu"])->where($map)->order("sort desc")->select();
        } else {
            //否则
            $userInfo = db($this->config['authUser'])->where(array("id" => $uid))->find();
            if (!empty($userInfo)) {
                $rules = db($this->config['group'])->where(array("id" => $userInfo['group_id']))->value("rules");
                $map['id'] = ['in', explode(",", $rules)];
                $addAuthMenu = db($this->config['authMenu'])->where($map)->order("sort desc")->select();
            }
        }
        return $addAuthMenu;
    }

    // 检测权限
    public function check($url = "", $uid = "")
    {
        if ($uid == "") {
            $uid = getLoginUserId();
        }
        $addAuthMenu = $this->getrule($uid);
        $authMenuList = [];
        foreach ($addAuthMenu as $k => $v) {
            $authMenuList[$k] = strtolower($v['link']);
        }
        $strtolowerUrl = strtolower($url);
        if (in_array($strtolowerUrl, $authMenuList)) {
            return true;
        } else {
            return false;
        }
    }
}