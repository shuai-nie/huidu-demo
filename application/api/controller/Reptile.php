<?php
namespace app\api\controller;

use app\admin\model\Content;
use app\admin\model\ContentDetail;
use lib\Reptile as ApiReptile;
use think\Controller;

class Reptile extends Controller
{
    public function index()
    {
        $BeginTime = microtime(true);
        $data = (new ApiReptile())->apiCifNewsBrandFacebook();
        foreach($data as $k => $val){
            $count = Content::where(['title'=>$val['title']])->count();
            if($count == 0){
                $local = (new ApiReptile())->getRemoteFileToLocal($val['imgUrl'], ROOT_PATH . 'public/uploads/reptile/');
                if($local['code'] == 1){
                    $val['imgUrl'] = $local['path'];
                }else{
                    $val['imgUrl'] = '';
                }
                $val['detail'] = (new ApiReptile())->CifNewsArticle($val['link']);

                $content = Content::create([
                    'title' => $val['title'],
                    'category_id' => 0,
                    'intro' => $val['describes'],
                    'cover_url' => $val['imgUrl'],
                    'isweb' => 11,
                ]);
                ContentDetail::create([
                    'cid' => $content->id,
                    'content' => $val['detail'],
                ]);
            }
        }
        $EndTime = microtime(true);

        exit($EndTime-$BeginTime);


    }
}