<?php
namespace app\admin\controller;

use app\admin\model\ContentCategory;
use lib\Reptile as ApiReptile;
use app\admin\model\Reptile as ReptileModel;
use app\admin\model\ContentProperty ;

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
            $ContentCategoryTable = (new ContentCategory())->getTable();
            $data = ReptileModel::alias('A')
                ->join([$ContentCategoryTable => 'B'], 'A.type=B.id', 'left')
                ->field('A.*,B.name as category_name')
                ->where($map)->select();
            $count = ReptileModel::alias('A')
                ->join([$ContentCategoryTable => 'B'], 'A.type=B.id', 'left')
                ->where($map)->count();
            foreach ($data as $key => $val){
                if(!empty($val['attribute'])){
                    //$attribute = explode(',', $val['attribute']);
                    $pro = ContentProperty::where(['id'=>['in', $val['attribute']]])->select();
                    $proArr = [];
                    foreach ($pro as $k => $v){
                         array_push($proArr, $v['name']);
                    }
                    $val['attribute'] = implode(',', $proArr);
                }
                $data[$key] = $val;
            }

            return json(['count' => 0, 'msg' => '', 'data' => ['count' => $count, 'list' => $data]]);
        }
        return view('', [
            'type' => (new ReptileModel())->type,
            'attribute' => (new ReptileModel())->attribute,
            'meta_title' => '爬虫管理',
        ]);
    }

    public function edit()
    {
        $id = request()->param('id');
        if(request()->isPost()){
            $_post = request()->post();
            $_post['attribute'] = isset($_post['attribute']) ? implode(',', $_post['attribute']) : '';
            $state = ReptileModel::update($_post, ['id'=>$id]);
            if($state != false){
                return success_json('提交成功');
            }
            return error_json('提交失败');
        }
        $CategoryAll = \app\admin\model\ContentCategory::where(['is_del'=>0])->select();
        $PropertyAll = \app\admin\model\ContentProperty::where(['status'=>1])->select();
        $data = ReptileModel::where(['id'=>$id])->find();
        $data['attribute'] = explode(',', $data['attribute']);
        return view('', [
            'CategoryAll' => $CategoryAll,
            'PropertyAll' => $PropertyAll,
            'data' => $data
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