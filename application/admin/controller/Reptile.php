<?php
namespace app\admin\controller;

use lib\Reptile as ApiReptile;
use app\admin\model\Reptile as ReptileModel;

class Reptile extends Base
{
    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        if(request()->isPost()){
            $map = [];
            $data = ReptileModel::where($map)->select();
            $count = ReptileModel::where($map)->count();
            return json(['count' => 0, 'msg' => '', 'data' => ['count' => $count, 'list' => $data]]);
        }
        return view('', [
            'type' => (new ReptileModel())->type,
            'attribute' => (new ReptileModel())->attribute,
            'meta_title' => '爬虫来源',
        ]);
    }

    public function filter()
    {
        if(request()->isPost()){
            $_post = request()->post();
            $state = \app\admin\model\Config::update(['value' => $_post['value']], ['id' => $_post['id']]);
            if($state != false){
                return success_json('提交成功');
            }
            return error_json('提交失败');
        }
        $list = \app\admin\model\Config::where(['id'=>100])->find();
//        $value = $list['value'];
//        $str = "测试17";
//        if(!empty($value)){
//            $value = explode("\n", $value);
//            foreach ($value as $val){
//                $v = explode("=", $val);
//                if(isset($v[0]) && isset($v[1])){
//                    $this->strReplace($v, $str);
//
//                }
//            }
//        }
        return view('', [
            'meta_title' => '过滤关键词',
            'list' => $list
        ]);
    }

    public function article()
    {
        $data = (new ApiReptile())->CifNewsArticle();
        var_dump($data);
    }

    public function image()
    {

//        //记录程序开始的时间
//        $BeginTime = microtime(true);
//        $img= (new ApiReptile())->getImage("https://www.php.cn/static/images/examples/text17.png","", '', 1);
//        if($img):echo '<pre><img src="http://thinkcms.nf/'.$img.'"></pre>';else:echo "false";endif;
////记录程序运行结束的时间
//        $EndTime = microtime(true);
////返回运行时间
//
//        exit($EndTime-$BeginTime);
//exit();
        $url = "https://img.cifnews.com/dev/20221114/4ede6c6ef44f44c8aa3d713d48f8156a.jpeg";
        $data = (new ApiReptile())->getRemoteFileToLocal($url, ROOT_PATH . 'public/uploads/');
        var_dump($data);exit();
    }




}