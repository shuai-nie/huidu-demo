<?php
namespace app\api\controller;

use app\admin\model\Content;
use app\admin\model\ContentDetail;
use app\admin\model\ContentPropertyRelevance;
use lib\Reptile as ApiReptile;
use think\Controller;
use app\admin\model\AdminLog;

class Reptile extends Controller
{
    public function index()
    {
        (new ApiReptile())->CifNewsArticle('');
            exit();
        $BeginTime = microtime(true);
        $data = (new ApiReptile())->apiCifNewsBrandFacebook();
        $c = 0;
        $ReptileInfo = \app\admin\model\Reptile::where(['id'=>11])->find();
        $ReptileInfo['attribute'] = explode(',', $ReptileInfo['attribute']);
        $list = \app\admin\model\Config::where(['id'=>100])->find();
        foreach($data as $k => $val){
            if(!empty($list['value'])){
                $value = explode("\n", $list['value']);
                foreach ($value as $valCon){
                    $vCon = explode("=", $valCon);
                    if(isset($vCon[0]) && isset($vCon[1])){
                        (new ApiReptile())->strReplace($vCon, $val['title']);
                    }
                }
            }
            $count = Content::where(['title'=>$val['title']])->count();
            if($count == 0){
                $c++;
                $local = (new ApiReptile())->getRemoteFileToLocal($val['imgUrl'], ROOT_PATH . 'public/uploads/reptile/');
                if($local['code'] == 1){
                    $val['imgUrl'] = $local['path'];
                }else{
                    $val['imgUrl'] = 'http://img.91po.net/cover_url/2022111700'.mt_rand(1, 5).'.jpg';
                }

                $val['detail'] = (new ApiReptile())->CifNewsArticle($val['link']);
                $val['detail'] = str_replace('图虫创意', '', $val['detail']);
                $val['detail'] = str_replace('图片来源：', '', $val['detail']);
                $val['detail'] = str_replace('</p>', '</p><br/>', $val['detail']);
                if(!empty($list['value'])){
                    $value = explode("\n", $list['value']);
                    foreach ($value as $valCon){
                        $vCon = explode("=", $valCon);
                        if(isset($vCon[0]) && isset($vCon[1])){
                            (new ApiReptile())->strReplace($vCon, $val['detail']);
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
                    'create_id' => 0,
                    'update_id' => 0,
                ]);
                $content_id = $content->id;
                ContentDetail::create([
                    'cid' => $content_id,
                    'content' => $val['detail'].'<p style="text-indent: 2em;"><br/></p><p style="text-indent: 2em;"><br/></p><p style="text-indent: 2em;"><br/></p><p style="text-indent: 2em;"><br/></p><p><span style="font-size: 18px;"><strong>○ 海量供应需求资源对接&nbsp; ○ 链接精英<strong><a href="https://www.huidu.io/news/1629/" target="_blank">出海</a></strong>人脉&nbsp; ○ 免费发布业务需求</strong></span></p><p><span style="font-size: 18px;"><strong>欢迎加入 <strong><a href="https://t.me/HUIDUZ" target="_blank" ref="nofollow">灰度-海外资源交流群</a></strong> <strong><a href="https://t.me/HUIDUZ" target="_blank" ref="nofollow">@HUIDUZ</a></strong></strong></span></p><p><span style="font-size: 18px;"><strong>商务合作：<strong><a href="https://t.me/HD_sevens" target="_blank" ref="nofollow">@HD_sevens</a></strong>&nbsp; <strong><a href="https://t.me/HuiduDy" target="_blank" ref="nofollow">@HuiduDy</a></strong></strong></span></p><p style="text-indent: 2em;"><br/></p>',

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
        AdminLog::create(['uid' => 0,'text' => '爬虫脚本11','url' => (string)request()->url(),'ip' => request()->ip() ]);

        exit($EndTime-$BeginTime);


    }
}