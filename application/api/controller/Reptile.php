<?php
namespace app\api\controller;

use app\admin\model\Content;
use app\admin\model\ContentDetail;
use app\admin\model\ContentPropertyRelevance;
use lib\Reptile as ApiReptile;
use think\Controller;

class Reptile extends Controller
{
    public function index()
    {
        $BeginTime = microtime(true);
        $data = (new ApiReptile())->apiCifNewsBrandFacebook();
        $c = 0;
        $ReptileInfo = \app\admin\model\Reptile::where(['id'=>11])->find();
        $ReptileInfo['attribute'] = explode(',', $ReptileInfo['attribute']);
        foreach($data as $k => $val){
            $count = Content::where(['title'=>$val['title']])->count();
            if($count == 0){
                $c++;
                $local = (new ApiReptile())->getRemoteFileToLocal($val['imgUrl'], ROOT_PATH . 'public/uploads/reptile/');
                if($local['code'] == 1){
                    $val['imgUrl'] = $local['path'];
                }else{
                    $val['imgUrl'] = '';
                }
                $val['detail'] = (new ApiReptile())->CifNewsArticle($val['link']);

                $content = Content::create([
                    'title' => $val['title'],
                    'category_id' => $ReptileInfo['type'],
                    'intro' => $val['describes'],
                    'cover_url' => $val['imgUrl'],
                    'isweb' => 11,
                ]);
                $content_id = $content->id;
                ContentDetail::create([
                    'cid' => $content_id,
                    'content' => $val['detail'],
                ]);
                foreach ($ReptileInfo['attribute'] as $attr){
                    ContentPropertyRelevance::create([
                       'property_id' => $attr,
                       'content_id' => $content_id,
                       'status' => 1,
                    ]);
                }
            }
        }
        $EndTime = microtime(true);
        \app\admin\model\Reptile::where(['id'=>11])->setInc('total', $c);

        exit($EndTime-$BeginTime);


    }
}