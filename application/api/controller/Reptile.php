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

                $list = \app\admin\model\Config::where(['id'=>100])->find();
                if(!empty($list['value'])){
                    $value = explode("\n", $list['value']);
                    foreach ($value as $valCon){
                        $vCon = explode("=", $valCon);
                        if(isset($vCon[0]) && isset($vCon[1])){
                            (new ApiReptile())->strReplace($vCon, $val['detail']);
                            (new ApiReptile())->strReplace($vCon, $val['title']);
                            (new ApiReptile())->strReplace($vCon, $val['describes']);
                        }
                    }
                }

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
                    'content' => $val['detail'].'<p style="text-indent: 2em;"><br/></p><p style="text-indent: 2em;"><br/></p><p style="text-indent: 2em;"><br/></p><p style="text-indent: 2em;"><br/></p><p><span style="font-size: 18px;"><strong>○ 海量供应需求资源对接&nbsp; ○ 链接精英出海人脉&nbsp; ○ 免费发布业务需求</strong></span></p><p><span style="font-size: 18px;"><strong>欢迎加入 灰度-海外资源交流群 @HUIDUZ</strong></span></p><p><span style="font-size: 18px;"><strong>商务合作：@HD_sevens&nbsp; @HuiduDy</strong></span></p><p style="text-indent: 2em;"><br/></p>',
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