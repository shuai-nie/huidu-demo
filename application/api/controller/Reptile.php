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
    public $lxfs = '<p style="text-indent: 2em;"><br/></p><p style="text-indent: 2em;"><br/></p><p style="text-indent: 2em;"><br/></p><p style="text-indent: 2em;"><br/></p><p><span style="font-size: 18px;"><strong>○ 海量供应需求资源对接&nbsp; ○ 链接精英<strong><a href="https://www.huidu.io/news/1629/" target="_blank">出海</a></strong>人脉&nbsp; ○ 免费发布业务需求</strong></span></p><p><span style="font-size: 18px;"><strong>欢迎加入 <strong><a href="https://t.me/HUIDUZ" target="_blank" ref="nofollow">灰度-海外资源交流群</a></strong> <strong><a href="https://t.me/HUIDUZ" target="_blank" ref="nofollow">@HUIDUZ</a></strong></strong></span></p><p><span style="font-size: 18px;"><strong>商务合作：<strong><a href="https://t.me/HDseven777" target="_blank" ref="nofollow">@HDseven777</a></strong>&nbsp; <strong><a href="https://t.me/HuiduDy" target="_blank" ref="nofollow">@HuiduDy</a></strong></strong></span></p><p style="text-indent: 2em;"><br/></p>';

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
                    'content' => $val['detail'] . $this->lxfs,

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
                    'content' => $val['detail'] . $this->lxfs,

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
                    'content' => $val['detail'] . $this->lxfs,

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
                    'content' => $val['detail'] . $this->lxfs,

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
                    'content' => $val['detail'] . $this->lxfs,
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

    public function tags_google()
    {
        $id = 16;
        $BeginTime = microtime(true);
        $data = (new ApiReptile())->tagsGoogle("https://www.kchuhai.com/tags/Google");
        $c = 0;
        $doc = new \DOMDocument();
        $reg1="/<a .*?>.*?<\/a>/";
        $reg2 = "/<div class=\"w-490 h-40 font-14 text-999 text-ellipsis2 mb-3\"([\S\s]+?)<\/div>/";

        $ReptileInfo = \app\admin\model\Reptile::where(['id' => $id])->find();
        $ReptileInfo['attribute'] = explode(',', $ReptileInfo['attribute']);
        $list = \app\admin\model\Config::where(['id' => 100])->find();

        foreach ($data as $val){
            $libxml_previous_state = libxml_use_internal_errors(true);
            $doc->loadHTML($val);
            libxml_clear_errors();
            $xpath = new \DOMXPath($doc);
            libxml_use_internal_errors($libxml_previous_state);
            $src = $xpath->evaluate("string(//img/@src)");
            $href = $xpath->evaluate("string(//a/@href)");
            preg_match_all($reg2, $val, $describes);
            preg_match_all($reg1, $val,$title);
            $title[0][0] = strip_tags($title[0][0]);
            $describes[0][0] = strip_tags($describes[0][0]);

            if(!empty($title[0][0])){
                if (!empty($list['value'])) {
                    $value = explode("\n", $list['value']);
                    foreach ($value as $valCon) {
                        $vCon = explode("=", $valCon);
                        if (isset($vCon[0]) && isset($vCon[1])) {
                            (new ApiReptile())->strReplace($vCon, $title[0][0]);
                        }
                    }
                }

                $count = Content::where(['title' => $title[0][0] ])->count();
                if ($count == 0) {
                    $local = (new ApiReptile())->getRemoteFileToLocal($src, ROOT_PATH . 'public/uploads/reptile/');
                    if ($local['code'] == 1) {
                        $img = (new Upload())->fileUpload(ROOT_PATH . 'public/uploads/reptile/' . $local['path']);
                    } else {
                        $img = 'https://huidu-bucket.s3.ap-southeast-1.amazonaws.com/huidu/cover_url/2022111700' . mt_rand(1, 5) . '.jpg';
                    }

                    $desc_kchuhai = (new ApiReptile())->kchuhai_desc($href);

                    if (!empty($list['value'])) {
                        $value = explode("\n", $list['value']);
                        foreach ($value as $valCon) {
                            $vCon = explode("=", $valCon);
                            if (isset($vCon[0]) && isset($vCon[1])) {
                                (new ApiReptile())->strReplace($vCon, $desc_kchuhai);
                                (new ApiReptile())->strReplace($vCon, $describes[0][0]);
                            }
                        }
                    }

                    $content    = Content::create([
                        'title'       => $title[0][0],
                        'category_id' => $ReptileInfo['type'],
                        'intro'       => $describes[0][0],
                        'cover_url'   => $img,
                        'isweb'       => $id,
                        'create_id'   => 0,
                        'update_id'   => 0,
                    ]);
                    $content_id = $content->id;
                    ContentDetail::create([
                        'cid'     => $content_id,
                        'content' => $desc_kchuhai . $this->lxfs,

                    ]);
                    foreach ($ReptileInfo['attribute'] as $attr) {
                        ContentPropertyRelevance::create([
                            'property_id' => $attr,
                            'content_id'  => $content_id,
                            'status'      => 1,
                        ]);
                    }
                    $c++;
                }
            }
        }
        $EndTime = microtime(true);
        \app\admin\model\Reptile::where(['id' => $id])->setInc('total', $c);
        AdminLog::create(['uid' => 0, 'text' => '爬虫脚本' . $id . "|" . ($EndTime - $BeginTime), 'url' => (string)request()->url(), 'ip' => request()->ip()]);
        exit($EndTime - $BeginTime);
    }

    // 快出海-Google Adwords：
    public function google_adwords()
    {
        $id = 17;
        $BeginTime = microtime(true);
        $data = (new ApiReptile())->tagsGoogle("https://www.kchuhai.com/tags/Google-Adwords");
        $c = 0;
        $doc = new \DOMDocument();
        $reg1="/<a .*?>.*?<\/a>/";
        $reg2 = "/<div class=\"w-490 h-40 font-14 text-999 text-ellipsis2 mb-3\"([\S\s]+?)<\/div>/";

        $ReptileInfo = \app\admin\model\Reptile::where(['id' => $id])->find();
        $ReptileInfo['attribute'] = explode(',', $ReptileInfo['attribute']);
        $list = \app\admin\model\Config::where(['id' => 100])->find();

        foreach ($data as $val){
            $libxml_previous_state = libxml_use_internal_errors(true);
            $doc->loadHTML($val);
            libxml_clear_errors();
            $xpath = new \DOMXPath($doc);
            libxml_use_internal_errors($libxml_previous_state);
            $src = $xpath->evaluate("string(//img/@src)");
            $href = $xpath->evaluate("string(//a/@href)");
            preg_match_all($reg2, $val, $describes);
            preg_match_all($reg1, $val,$title);
            $title[0][0] = strip_tags($title[0][0]);
            $describes[0][0] = strip_tags($describes[0][0]);

            if(!empty($title[0][0])){
                if (!empty($list['value'])) {
                    $value = explode("\n", $list['value']);
                    foreach ($value as $valCon) {
                        $vCon = explode("=", $valCon);
                        if (isset($vCon[0]) && isset($vCon[1])) {
                            (new ApiReptile())->strReplace($vCon, $title[0][0]);
                        }
                    }
                }

                $count = Content::where(['title' => $title[0][0] ])->count();
                if ($count == 0) {
                    $local = (new ApiReptile())->getRemoteFileToLocal($src, ROOT_PATH . 'public/uploads/reptile/');
                    if ($local['code'] == 1) {
                        $img = (new Upload())->fileUpload(ROOT_PATH . 'public/uploads/reptile/' . $local['path']);
                    } else {
                        $img = 'https://huidu-bucket.s3.ap-southeast-1.amazonaws.com/huidu/cover_url/2022111700' . mt_rand(1, 5) . '.jpg';
                    }

                    $desc_kchuhai = (new ApiReptile())->kchuhai_desc($href);

                    if (!empty($list['value'])) {
                        $value = explode("\n", $list['value']);
                        foreach ($value as $valCon) {
                            $vCon = explode("=", $valCon);
                            if (isset($vCon[0]) && isset($vCon[1])) {
                                (new ApiReptile())->strReplace($vCon, $desc_kchuhai);
                                (new ApiReptile())->strReplace($vCon, $describes[0][0]);
                            }
                        }
                    }

                    $content    = Content::create([
                        'title'       => $title[0][0],
                        'category_id' => $ReptileInfo['type'],
                        'intro'       => $describes[0][0],
                        'cover_url'   => $img,
                        'isweb'       => $id,
                        'create_id'   => 0,
                        'update_id'   => 0,
                    ]);
                    $content_id = $content->id;
                    ContentDetail::create([
                        'cid'     => $content_id,
                        'content' => $desc_kchuhai . $this->lxfs,
                    ]);
                    foreach ($ReptileInfo['attribute'] as $attr) {
                        ContentPropertyRelevance::create([
                            'property_id' => $attr,
                            'content_id'  => $content_id,
                            'status'      => 1,
                        ]);
                    }
                    $c++;
                }
            }
        }
        $EndTime = microtime(true);
        \app\admin\model\Reptile::where(['id' => $id])->setInc('total', $c);
        AdminLog::create(['uid' => 0, 'text' => '爬虫脚本' . $id . "|" . ($EndTime - $BeginTime), 'url' => (string)request()->url(), 'ip' => request()->ip()]);
        exit($EndTime - $BeginTime);
    }

    // 快出海-Tiktok
    public function tags_tiktok()
    {
        $id = 18;
        $BeginTime = microtime(true);
        $data = (new ApiReptile())->tagsGoogle("https://www.kchuhai.com/tags/TikTok");
        $c = 0;
        $doc = new \DOMDocument();
        $reg1="/<a .*?>.*?<\/a>/";
        $reg2 = "/<div class=\"w-490 h-40 font-14 text-999 text-ellipsis2 mb-3\"([\S\s]+?)<\/div>/";

        $ReptileInfo = \app\admin\model\Reptile::where(['id' => $id])->find();
        $ReptileInfo['attribute'] = explode(',', $ReptileInfo['attribute']);
        $list = \app\admin\model\Config::where(['id' => 100])->find();

        foreach ($data as $val){
            $libxml_previous_state = libxml_use_internal_errors(true);
            $doc->loadHTML($val);
            libxml_clear_errors();
            $xpath = new \DOMXPath($doc);
            libxml_use_internal_errors($libxml_previous_state);
            $src = $xpath->evaluate("string(//img/@src)");
            $href = $xpath->evaluate("string(//a/@href)");

            preg_match_all($reg2, $val, $describes);
            preg_match_all($reg1, $val,$title);
            $title[0][0] = strip_tags($title[0][0]);
            $describes[0][0] = strip_tags($describes[0][0]);

            if(!empty($title[0][0])){
                if (!empty($list['value'])) {
                    $value = explode("\n", $list['value']);
                    foreach ($value as $valCon) {
                        $vCon = explode("=", $valCon);
                        if (isset($vCon[0]) && isset($vCon[1])) {
                            (new ApiReptile())->strReplace($vCon, $title[0][0]);
                        }
                    }
                }

                $count = Content::where(['title' => $title[0][0] ])->count();
                if ($count == 0) {
                    $local = (new ApiReptile())->getRemoteFileToLocal($src, ROOT_PATH . 'public/uploads/reptile/');
                    if ($local['code'] == 1) {
                        $img = (new Upload())->fileUpload(ROOT_PATH . 'public/uploads/reptile/' . $local['path']);
                    } else {
                        $img = 'https://huidu-bucket.s3.ap-southeast-1.amazonaws.com/huidu/cover_url/2022111700' . mt_rand(1, 5) . '.jpg';
                    }


                    $desc_kchuhai = (new ApiReptile())->kchuhai_desc($href);
                    if (!empty($list['value'])) {
                        $value = explode("\n", $list['value']);
                        foreach ($value as $valCon) {
                            $vCon = explode("=", $valCon);
                            if (isset($vCon[0]) && isset($vCon[1])) {
                                (new ApiReptile())->strReplace($vCon, $desc_kchuhai);
                                (new ApiReptile())->strReplace($vCon, $describes[0][0]);
                            }
                        }
                    }
                    $content    = Content::create([
                        'title'       => $title[0][0],
                        'category_id' => $ReptileInfo['type'],
                        'intro'       => $describes[0][0],
                        'cover_url'   => $img,
                        'isweb'       => $id,
                        'create_id'   => 0,
                        'update_id'   => 0,
                    ]);
                    $content_id = $content->id;
                    ContentDetail::create([
                        'cid'     => $content_id,
                        'content' => $desc_kchuhai . $this->lxfs ,
                    ]);
                    foreach ($ReptileInfo['attribute'] as $attr) {
                        ContentPropertyRelevance::create([
                            'property_id' => $attr,
                            'content_id'  => $content_id,
                            'status'      => 1,
                        ]);
                    }
                    $c++;
                }
            }
        }
        $EndTime = microtime(true);
        \app\admin\model\Reptile::where(['id' => $id])->setInc('total', $c);
        AdminLog::create(['uid' => 0, 'text' => '爬虫脚本' . $id . "|" . ($EndTime - $BeginTime), 'url' => (string)request()->url(), 'ip' => request()->ip()]);
        exit($EndTime - $BeginTime);
    }

    public function kol_yingxiao()
    {
        $id = 19;
        $BeginTime = microtime(true);
        $data = (new ApiReptile())->tagsGoogle("https://www.kchuhai.com/tags/KOLyingxiao");
        $c = 0;
        $doc = new \DOMDocument();
        $reg1="/<a .*?>.*?<\/a>/";
        $reg2 = "/<div class=\"w-490 h-40 font-14 text-999 text-ellipsis2 mb-3\"([\S\s]+?)<\/div>/";

        $ReptileInfo = \app\admin\model\Reptile::where(['id' => $id])->find();
        $ReptileInfo['attribute'] = explode(',', $ReptileInfo['attribute']);
        $list = \app\admin\model\Config::where(['id' => 100])->find();

        foreach ($data as $val){
            $libxml_previous_state = libxml_use_internal_errors(true);
            $doc->loadHTML($val);
            libxml_clear_errors();
            $xpath = new \DOMXPath($doc);
            libxml_use_internal_errors($libxml_previous_state);
            $src = $xpath->evaluate("string(//img/@src)");
            $href = $xpath->evaluate("string(//a/@href)");

            preg_match_all($reg2, $val, $describes);
            preg_match_all($reg1, $val,$title);
            $title[0][0] = strip_tags($title[0][0]);
            $describes[0][0] = strip_tags($describes[0][0]);

            if(!empty($title[0][0])){
                if (!empty($list['value'])) {
                    $value = explode("\n", $list['value']);
                    foreach ($value as $valCon) {
                        $vCon = explode("=", $valCon);
                        if (isset($vCon[0]) && isset($vCon[1])) {
                            (new ApiReptile())->strReplace($vCon, $title[0][0]);
                        }
                    }
                }

                $count = Content::where(['title' => $title[0][0] ])->count();
                if ($count == 0) {
                    $local = (new ApiReptile())->getRemoteFileToLocal($src, ROOT_PATH . 'public/uploads/reptile/');
                    if ($local['code'] == 1) {
                        $img = (new Upload())->fileUpload(ROOT_PATH . 'public/uploads/reptile/' . $local['path']);
                    } else {
                        $img = 'https://huidu-bucket.s3.ap-southeast-1.amazonaws.com/huidu/cover_url/2022111700' . mt_rand(1, 5) . '.jpg';
                    }


                    $desc_kchuhai = (new ApiReptile())->kchuhai_desc($href);
                    if (!empty($list['value'])) {
                        $value = explode("\n", $list['value']);
                        foreach ($value as $valCon) {
                            $vCon = explode("=", $valCon);
                            if (isset($vCon[0]) && isset($vCon[1])) {
                                (new ApiReptile())->strReplace($vCon, $desc_kchuhai);
                                (new ApiReptile())->strReplace($vCon, $describes[0][0]);
                            }
                        }
                    }
                    $content    = Content::create([
                        'title'       => $title[0][0],
                        'category_id' => $ReptileInfo['type'],
                        'intro'       => $describes[0][0],
                        'cover_url'   => $img,
                        'isweb'       => $id,
                        'create_id'   => 0,
                        'update_id'   => 0,
                    ]);
                    $content_id = $content->id;
                    ContentDetail::create([
                        'cid'     => $content_id,
                        'content' => $desc_kchuhai . $this->lxfs ,
                    ]);
                    foreach ($ReptileInfo['attribute'] as $attr) {
                        ContentPropertyRelevance::create([
                            'property_id' => $attr,
                            'content_id'  => $content_id,
                            'status'      => 1,
                        ]);
                    }
                    $c++;
                }
            }
        }
        $EndTime = microtime(true);
        \app\admin\model\Reptile::where(['id' => $id])->setInc('total', $c);
        AdminLog::create(['uid' => 0, 'text' => '爬虫脚本' . $id . "|" . ($EndTime - $BeginTime), 'url' => (string)request()->url(), 'ip' => request()->ip()]);
        exit($EndTime - $BeginTime);
    }

    public function tags_facebook()
    {
        $id = 20;
        $BeginTime = microtime(true);
        $data = (new ApiReptile())->tagsGoogle("https://www.kchuhai.com/tags/Facebook");
        $c = 0;
        $doc = new \DOMDocument();
        $reg1="/<a .*?>.*?<\/a>/";
        $reg2 = "/<div class=\"w-490 h-40 font-14 text-999 text-ellipsis2 mb-3\"([\S\s]+?)<\/div>/";

        $ReptileInfo = \app\admin\model\Reptile::where(['id' => $id])->find();
        $ReptileInfo['attribute'] = explode(',', $ReptileInfo['attribute']);
        $list = \app\admin\model\Config::where(['id' => 100])->find();

        foreach ($data as $val){
            $libxml_previous_state = libxml_use_internal_errors(true);
            $doc->loadHTML($val);
            libxml_clear_errors();
            $xpath = new \DOMXPath($doc);
            libxml_use_internal_errors($libxml_previous_state);
            $src = $xpath->evaluate("string(//img/@src)");
            $href = $xpath->evaluate("string(//a/@href)");

            preg_match_all($reg2, $val, $describes);
            preg_match_all($reg1, $val,$title);
            $title[0][0] = strip_tags($title[0][0]);
            $describes[0][0] = strip_tags($describes[0][0]);

            if(!empty($title[0][0])){
                if (!empty($list['value'])) {
                    $value = explode("\n", $list['value']);
                    foreach ($value as $valCon) {
                        $vCon = explode("=", $valCon);
                        if (isset($vCon[0]) && isset($vCon[1])) {
                            (new ApiReptile())->strReplace($vCon, $title[0][0]);
                        }
                    }
                }

                $count = Content::where(['title' => $title[0][0] ])->count();
                if ($count == 0) {
                    $local = (new ApiReptile())->getRemoteFileToLocal($src, ROOT_PATH . 'public/uploads/reptile/');
                    if ($local['code'] == 1) {
                        $img = (new Upload())->fileUpload(ROOT_PATH . 'public/uploads/reptile/' . $local['path']);
                    } else {
                        $img = 'https://huidu-bucket.s3.ap-southeast-1.amazonaws.com/huidu/cover_url/2022111700' . mt_rand(1, 5) . '.jpg';
                    }


                    $desc_kchuhai = (new ApiReptile())->kchuhai_desc($href);
                    if (!empty($list['value'])) {
                        $value = explode("\n", $list['value']);
                        foreach ($value as $valCon) {
                            $vCon = explode("=", $valCon);
                            if (isset($vCon[0]) && isset($vCon[1])) {
                                (new ApiReptile())->strReplace($vCon, $desc_kchuhai);
                                (new ApiReptile())->strReplace($vCon, $describes[0][0]);
                            }
                        }
                    }
                    $content    = Content::create([
                        'title'       => $title[0][0],
                        'category_id' => $ReptileInfo['type'],
                        'intro'       => $describes[0][0],
                        'cover_url'   => $img,
                        'isweb'       => $id,
                        'create_id'   => 0,
                        'update_id'   => 0,
                    ]);
                    $content_id = $content->id;
                    ContentDetail::create([
                        'cid'     => $content_id,
                        'content' => $desc_kchuhai . $this->lxfs ,
                    ]);
                    foreach ($ReptileInfo['attribute'] as $attr) {
                        ContentPropertyRelevance::create([
                            'property_id' => $attr,
                            'content_id'  => $content_id,
                            'status'      => 1,
                        ]);
                    }
                    $c++;
                }
            }
        }
        $EndTime = microtime(true);
        \app\admin\model\Reptile::where(['id' => $id])->setInc('total', $c);
        AdminLog::create(['uid' => 0, 'text' => '爬虫脚本' . $id . "|" . ($EndTime - $BeginTime), 'url' => (string)request()->url(), 'ip' => request()->ip()]);
        exit($EndTime - $BeginTime);
    }



}