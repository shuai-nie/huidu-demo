<?php
namespace app\api\controller;

use app\admin\controller\Upload;
use app\admin\model\Content;
use app\admin\model\ContentDetail;
use app\admin\model\ContentPropertyRelevance;
use lib\Reptile as ApiReptile;
use QL\QueryList;
use think\Controller;
use app\admin\model\AdminLog;
use think\Log;

class Reptile extends Controller
{
    public function index()
    {
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
                    $val['imgUrl'] = (new Upload())->fileUpload(ROOT_PATH.'public/uploads/reptile/'.$local['path']);
                }else{
                    $val['imgUrl'] = 'https://huidu-bucket.s3.ap-southeast-1.amazonaws.com/huidu/cover_url/2022111700' . mt_rand(1, 5) . '.jpg';
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
        AdminLog::create(['uid' => 0, 'text' => '爬虫脚本11' . "|" . ($EndTime - $BeginTime), 'url' => (string)request()->url(), 'ip' => request()->ip()]);
        exit($EndTime-$BeginTime);
    }

    public function brand_google()
    {
        $id = 12;
        $BeginTime = microtime(true);
        $data = (new ApiReptile())->brandGoogle();
        $c = 0;
        $ReptileInfo = \app\admin\model\Reptile::where(['id'=>$id])->find();
        $ReptileInfo['attribute'] = explode(',', $ReptileInfo['attribute']);
        $list = \app\admin\model\Config::where(['id'=>100])->find();
        foreach ($data as $key => $val){
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
                    $val['imgUrl'] = (new Upload())->fileUpload(ROOT_PATH.'public/uploads/reptile/'.$local['path']);
                }else{
                    $val['imgUrl'] = 'https://huidu-bucket.s3.ap-southeast-1.amazonaws.com/huidu/cover_url/2022111700' . mt_rand(1, 5) . '.jpg';
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
                    'isweb' => $id,
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
        \app\admin\model\Reptile::where(['id'=>$id])->setInc('total', $c);
        AdminLog::create(['uid' => 0, 'text' => '爬虫脚本' . $id . "|" . ($EndTime - $BeginTime), 'url' => (string)request()->url(), 'ip' => request()->ip()]);
        exit($EndTime-$BeginTime);


    }

    public function platformnews()
    {
        $id = 13;
        $BeginTime = microtime(true);
        $data = (new ApiReptile())->platformnews();
        $c = 0;
        $ReptileInfo = \app\admin\model\Reptile::where(['id' => $id])->find();
        $ReptileInfo['attribute'] = explode(',', $ReptileInfo['attribute']);
        $list = \app\admin\model\Config::where(['id' => 100])->find();
        foreach ($data as $key => $val) {
            if (!empty($list['value'])) {
                $value = explode("\n", $list['value']);
                foreach ($value as $valCon) {
                    $vCon = explode("=", $valCon);
                    if (isset($vCon[0]) && isset($vCon[1])) {
                        (new ApiReptile())->strReplace($vCon, $val['title']);
                    }
                }
            }

            $count = Content::where(['title' => $val['title']])->count();
            if ($count == 0) {
                $c++;
                $local = (new ApiReptile())->getRemoteFileToLocal($val['imgUrl'], ROOT_PATH . 'public/uploads/reptile/');
                if ($local['code'] == 1) {
                    $val['imgUrl'] = (new Upload())->fileUpload(ROOT_PATH . 'public/uploads/reptile/' . $local['path']);
                } else {
                    $val['imgUrl'] = 'https://huidu-bucket.s3.ap-southeast-1.amazonaws.com/huidu/cover_url/2022111700' . mt_rand(1, 5) . '.jpg';
                }

                $val['detail'] = (new ApiReptile())->CifNewsArticle($val['link']);
                $val['detail'] = str_replace('图虫创意', '', $val['detail']);
                $val['detail'] = str_replace('图片来源：', '', $val['detail']);
                $val['detail'] = str_replace('</p>', '</p><br/>', $val['detail']);
                if (!empty($list['value'])) {
                    $value = explode("\n", $list['value']);
                    foreach ($value as $valCon) {
                        $vCon = explode("=", $valCon);
                        if (isset($vCon[0]) && isset($vCon[1])) {
                            (new ApiReptile())->strReplace($vCon, $val['detail']);
                            (new ApiReptile())->strReplace($vCon, $val['describes']);
                        }
                    }
                }

                $content    = Content::create([
                    'title'       => $val['title'],
                    'category_id' => $ReptileInfo['type'],
                    'intro'       => $val['describes'],
                    'cover_url'   => $val['imgUrl'],
                    'isweb'       => $id,
                    'create_id'   => 0,
                    'update_id'   => 0,
                ]);
                $content_id = $content->id;
                ContentDetail::create([
                    'cid'     => $content_id,
                    'content' => $val['detail'] . '<p style="text-indent: 2em;"><br/></p><p style="text-indent: 2em;"><br/></p><p style="text-indent: 2em;"><br/></p><p style="text-indent: 2em;"><br/></p><p><span style="font-size: 18px;"><strong>○ 海量供应需求资源对接&nbsp; ○ 链接精英<strong><a href="https://www.huidu.io/news/1629/" target="_blank">出海</a></strong>人脉&nbsp; ○ 免费发布业务需求</strong></span></p><p><span style="font-size: 18px;"><strong>欢迎加入 <strong><a href="https://t.me/HUIDUZ" target="_blank" ref="nofollow">灰度-海外资源交流群</a></strong> <strong><a href="https://t.me/HUIDUZ" target="_blank" ref="nofollow">@HUIDUZ</a></strong></strong></span></p><p><span style="font-size: 18px;"><strong>商务合作：<strong><a href="https://t.me/HD_sevens" target="_blank" ref="nofollow">@HD_sevens</a></strong>&nbsp; <strong><a href="https://t.me/HuiduDy" target="_blank" ref="nofollow">@HuiduDy</a></strong></strong></span></p><p style="text-indent: 2em;"><br/></p>',

                ]);
                foreach ($ReptileInfo['attribute'] as $attr) {
                    ContentPropertyRelevance::create([
                        'property_id' => $attr,
                        'content_id'  => $content_id,
                        'status'      => 1,
                    ]);
                }
            }
        }

        $EndTime = microtime(true);
        \app\admin\model\Reptile::where(['id' => $id])->setInc('total', $c);
        AdminLog::create(['uid' => 0, 'text' => '爬虫脚本' . $id . "|" . ($EndTime - $BeginTime), 'url' => (string)request()->url(), 'ip' => request()->ip()]);
        exit($EndTime - $BeginTime);

    }

    public function facebook()
    {
        $id = 14;
        $BeginTime = microtime(true);
        $data = (new ApiReptile())->facebook();
        $c = 0;
        $ReptileInfo = \app\admin\model\Reptile::where(['id' => $id])->find();
        $ReptileInfo['attribute'] = explode(',', $ReptileInfo['attribute']);
        $list = \app\admin\model\Config::where(['id' => 100])->find();
        foreach ($data as $key => $val) {
//            if (!empty($list['value'])) {
//                $value = explode("\n", $list['value']);
//                foreach ($value as $valCon) {
//                    $vCon = explode("=", $valCon);
//                    if (isset($vCon[0]) && isset($vCon[1])) {
//                        (new ApiReptile())->strReplace($vCon, $val['title']);
//                    }
//                }
//            }

            $count = 0;//Content::where(['title' => $val['title']])->count();
            if ($count == 0) {
                $c++;
                $local = (new ApiReptile())->getRemoteFileToLocal($val['imgUrl'], ROOT_PATH . 'public/uploads/reptile/');
                if ($local['code'] == 1) {
                    $val['imgUrl'] = (new Upload())->fileUpload(ROOT_PATH . 'public/uploads/reptile/' . $local['path']);
                } else {
                    $val['imgUrl'] = 'https://huidu-bucket.s3.ap-southeast-1.amazonaws.com/huidu/cover_url/2022111700' . mt_rand(1, 5) . '.jpg';
                }

                $val['detail'] = (new ApiReptile())->CifNewsArticle($val['link']);
                $val['detail'] = str_replace('图虫创意', '', $val['detail']);
                $val['detail'] = str_replace('图片来源：', '', $val['detail']);
                $val['detail'] = str_replace('</p>', '</p><br/>', $val['detail']);

                if (!empty($list['value'])) {
                    $value = explode("\n", $list['value']);
                    foreach ($value as $valCon) {
                        $vCon = explode("=", $valCon);
                        if (isset($vCon[0]) && isset($vCon[1])) {
                            (new ApiReptile())->strReplace($vCon, $val['detail']);
                            (new ApiReptile())->strReplace($vCon, $val['describes']);
                        }
                    }
                }
                var_dump($val['detail']);exit();

//                $content    = Content::create([
//                    'title'       => $val['title'],
//                    'category_id' => $ReptileInfo['type'],
//                    'intro'       => $val['describes'],
//                    'cover_url'   => $val['imgUrl'],
//                    'isweb'       => $id,
//                    'create_id'   => 0,
//                    'update_id'   => 0,
//                ]);
//                $content_id = $content->id;
//                ContentDetail::create([
//                    'cid'     => $content_id,
//                    'content' => $val['detail'] . '<p style="text-indent: 2em;"><br/></p><p style="text-indent: 2em;"><br/></p><p style="text-indent: 2em;"><br/></p><p style="text-indent: 2em;"><br/></p><p><span style="font-size: 18px;"><strong>○ 海量供应需求资源对接&nbsp; ○ 链接精英<strong><a href="https://www.huidu.io/news/1629/" target="_blank">出海</a></strong>人脉&nbsp; ○ 免费发布业务需求</strong></span></p><p><span style="font-size: 18px;"><strong>欢迎加入 <strong><a href="https://t.me/HUIDUZ" target="_blank" ref="nofollow">灰度-海外资源交流群</a></strong> <strong><a href="https://t.me/HUIDUZ" target="_blank" ref="nofollow">@HUIDUZ</a></strong></strong></span></p><p><span style="font-size: 18px;"><strong>商务合作：<strong><a href="https://t.me/HD_sevens" target="_blank" ref="nofollow">@HD_sevens</a></strong>&nbsp; <strong><a href="https://t.me/HuiduDy" target="_blank" ref="nofollow">@HuiduDy</a></strong></strong></span></p><p style="text-indent: 2em;"><br/></p>',
//
//                ]);
//                foreach ($ReptileInfo['attribute'] as $attr) {
//                    ContentPropertyRelevance::create([
//                        'property_id' => $attr,
//                        'content_id'  => $content_id,
//                        'status'      => 1,
//                    ]);
//                }
            }
        }
        $EndTime = microtime(true);
//        \app\admin\model\Reptile::where(['id' => $id])->setInc('total', $c);
//        AdminLog::create(['uid' => 0, 'text' => '爬虫脚本' . $id . "|" . ($EndTime - $BeginTime), 'url' => (string)request()->url(), 'ip' => request()->ip()]);
        exit($EndTime - $BeginTime);
    }

    public function fuwutuijian()
    {
        $id = 15;
        $BeginTime = microtime(true);
        $data = (new ApiReptile())->fuwutuijian();
        $c = 0;
        $ReptileInfo = \app\admin\model\Reptile::where(['id' => $id])->find();
        $ReptileInfo['attribute'] = explode(',', $ReptileInfo['attribute']);
        $list = \app\admin\model\Config::where(['id' => 100])->find();
        foreach ($data as $key => $val){
            if (!empty($list['value'])) {
                $value = explode("\n", $list['value']);
                foreach ($value as $valCon) {
                    $vCon = explode("=", $valCon);
                    if (isset($vCon[0]) && isset($vCon[1])) {
                        (new ApiReptile())->strReplace($vCon, $val['title']);
                    }
                }
            }

            $count = Content::where(['title' => $val['title']])->count();
            if ($count == 0) {
                $c++;
                $local = (new ApiReptile())->getRemoteFileToLocal($val['img'], ROOT_PATH . 'public/uploads/reptile/');
                if ($local['code'] == 1) {
                    $val['img'] = (new Upload())->fileUpload(ROOT_PATH . 'public/uploads/reptile/' . $local['path']);
                } else {
                    $val['img'] = 'https://huidu-bucket.s3.ap-southeast-1.amazonaws.com/huidu/cover_url/2022111700' . mt_rand(1, 5) . '.jpg';
                }

                $val['detail'] = (new ApiReptile())->CifNewsArticle($val['url']);
                $val['detail'] = str_replace('图虫创意', '', $val['detail']);
                $val['detail'] = str_replace('图片来源：', '', $val['detail']);
                $val['detail'] = str_replace('</p>', '</p><br/>', $val['detail']);
                if (!empty($list['value'])) {
                    $value = explode("\n", $list['value']);
                    foreach ($value as $valCon) {
                        $vCon = explode("=", $valCon);
                        if (isset($vCon[0]) && isset($vCon[1])) {
                            (new ApiReptile())->strReplace($vCon, $val['detail']);
                            (new ApiReptile())->strReplace($vCon, $val['describes']);
                        }
                    }
                }

                $content    = Content::create([
                    'title'       => $val['title'],
                    'category_id' => $ReptileInfo['type'],
                    'intro'       => $val['describes'],
                    'cover_url'   => $val['img'],
                    'isweb'       => $id,
                    'create_id'   => 0,
                    'update_id'   => 0,
                ]);
                $content_id = $content->id;
                ContentDetail::create([
                    'cid'     => $content_id,
                    'content' => $val['detail'] . '<p style="text-indent: 2em;"><br/></p><p style="text-indent: 2em;"><br/></p><p style="text-indent: 2em;"><br/></p><p style="text-indent: 2em;"><br/></p><p><span style="font-size: 18px;"><strong>○ 海量供应需求资源对接&nbsp; ○ 链接精英<strong><a href="https://www.huidu.io/news/1629/" target="_blank">出海</a></strong>人脉&nbsp; ○ 免费发布业务需求</strong></span></p><p><span style="font-size: 18px;"><strong>欢迎加入 <strong><a href="https://t.me/HUIDUZ" target="_blank" ref="nofollow">灰度-海外资源交流群</a></strong> <strong><a href="https://t.me/HUIDUZ" target="_blank" ref="nofollow">@HUIDUZ</a></strong></strong></span></p><p><span style="font-size: 18px;"><strong>商务合作：<strong><a href="https://t.me/HD_sevens" target="_blank" ref="nofollow">@HD_sevens</a></strong>&nbsp; <strong><a href="https://t.me/HuiduDy" target="_blank" ref="nofollow">@HuiduDy</a></strong></strong></span></p><p style="text-indent: 2em;"><br/></p>',

                ]);
                foreach ($ReptileInfo['attribute'] as $attr) {
                    ContentPropertyRelevance::create([
                        'property_id' => $attr,
                        'content_id'  => $content_id,
                        'status'      => 1,
                    ]);
                }
            }
        }
        $EndTime = microtime(true);
        \app\admin\model\Reptile::where(['id' => $id])->setInc('total', $c);
        AdminLog::create(['uid' => 0, 'text' => '爬虫脚本' . $id . "|" . ($EndTime - $BeginTime), 'url' => (string)request()->url(), 'ip' => request()->ip()]);
        exit($EndTime - $BeginTime);
    }

    public function dd()
    {
//        $data = QueryList::get('https://zy.91po.net/2.html')->find('img')->attrs('src');
//        foreach ($data->all() as $val){
//            echo '"'.$val."\",<br/>";
//        }

//        $html = file_get_contents('https://zy.91po.net/2.html');
//        $data = QueryList::html($html)->query()->getData();
//        var_dump($data->all());
//        exit();
////        foreach ($data->all() as $val){
////            echo '"'.$val."\",<br/>";
////        }


    }

    public function cc()
    {
//        $data = (new ApiReptile())->GetHttp("https://zy.91po.net/2.html");
//        // <a.*?.<\/a>
//        preg_match_all('/<a.*?.<\/a>/', $data, $arr);
//        $doc = new \DOMDocument();
//        foreach ($arr[0] as $key => $val){
//            if($key%2==0){
//                $libxml_previous_state = libxml_use_internal_errors(true);
//                $doc->loadHTML($val);
//                libxml_clear_errors();
//                $xpath = new \DOMXPath($doc);
//                libxml_use_internal_errors($libxml_previous_state);
//                $src = $xpath->evaluate("string(//img/@src)");
//                $src = explode('/', $src);
//                $src = $src[count($src)-1];
//                echo "<img src='https://s3.eu-west-2.amazonaws.com/s3.dev.public.whale/currency_icon/".$src. "' art='".strip_tags( $arr[0][$key+1]). "' width='40' height='40' style='float:left;' />";
//
//
//            }
//
//        }

    }





}