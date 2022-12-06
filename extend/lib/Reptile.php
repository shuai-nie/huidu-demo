<?php

namespace lib;

use app\admin\controller\Upload;
use lib\Reptile as ApiReptile;

class Reptile
{
    // Facebook 海外营销列表
    public function apiCifNewsBrandFacebook()
    {
        $url = "https://www.cifnews.com/guoyuan/api/brand/facebook/all?size=10&page=1&key=google&code=all";
        $data =  $this->GetHttp($url);
        $data = json_decode($data, true);
        if($data['result'] === true){
            return $data['data'];
        }
        return false;
    }

    // Facebook 海外营销详情
    public function CifNewsArticle($url)
    {
        $data =  $this->GetHttp($url);
        $pos1 = strpos($data, "<div class=\"article-content article-inner leftcont");
        $pos2 = strpos($data, "<p> <strong>");
        $detail = substr($data, $pos1 , $pos2 - $pos1 );
        $detail = preg_replace('#alt="[^"]*"#i', '', $detail);
        preg_match_all("/<img.*\>/U", $detail, $img, PREG_PATTERN_ORDER);
//        header("Content-type:text/html;charset=utf-8");
//        var_dump($detail);
/*        $reg2 = "/[img|IMG].*?src=['|\"](.*?(?:[.gif|.jpg]))['|\"].*?[\/]?>/";*/
//        preg_match_all($reg2, $detail, $img, PREG_PATTERN_ORDER);
        $doc = new \DOMDocument();
        foreach ($img[0] as $val){
            $str = $val;
            $str = str_replace( "src=\"https://pic.cifnews.com/upload/202103/04/202103041710135519.jpg!/both/750x386\"", "", $str);
            $str = str_replace( "src=\"https://pic.cifnews.com/upload/202103/04/202103041710135519.jpg\"", "", $str);
            $str = str_replace( "data-src=\"", "src=\"", $str);
            $libxml_previous_state = libxml_use_internal_errors(true);
            $doc->loadHTML($str);
            libxml_clear_errors();
            $xpath = new \DOMXPath($doc);
            libxml_use_internal_errors($libxml_previous_state);
            $src = $xpath->evaluate("string(//img/@src)");

            if(!empty($src)){
                $parse_url = parse_url($src);
                $src = $parse_url['scheme'] . '://' . $parse_url['host'] . $parse_url['path'];
                $data = $this->getRemoteFileToLocal($src, ROOT_PATH . 'public/uploads/reptile/');
                if($data['code'] == 1){
                    $AwsImgUrl = (new Upload())->fileUpload(ROOT_PATH.'public/uploads/reptile/'.$data['path']);
                    $str = str_replace($src, $AwsImgUrl, $str);
                    $detail = str_replace($val, $str, $detail);
                }else{
                    $detail = str_replace($val, '', $detail);
                }
            }else{
                $detail = str_replace($val, '', $detail);
            }
        }
//        //$detail = str_replace('', '', $detail);
        // 去掉链接
        $detail = preg_replace("/<a[^>]*>(.*?)<\/a>/is", "$1", $detail);
        $detail .= "</div>";
        return $detail;
    }

    /**
     * 雨果跨境-google实操 12
     */
    public function brandGoogle()
    {
        $url = "https://www.cifnews.com/guoyuan/api/brand/google/all?size=10&page=1&key=google&code=all";
        $data =  $this->GetHttp($url);
        $data = json_decode($data, true);
        if($data['result'] === true){
            return $data['data'];
        }
        return false;
    }

    /**
     * 雨果跨境-google 资讯 13
     */
    public function platformnews()
    {
        $url = "https://www.cifnews.com/guoyuan/api/brand/google/platformnews?size=10&page=1&key=google&code=platformnews";
        $data =  $this->GetHttp($url);
        $data = json_decode($data, true);
        if($data['result'] === true){
            return $data['data'];
        }
        return false;
    }

    /**
     * 雨果跨境-facebook - 实操 14
     */
    public function facebook()
    {
        $url = "https://www.cifnews.com/guoyuan/api/brand/facebook/all?size=10&page=1&key=facebook&code=all";
        $data =  $this->GetHttp($url);
        $data = json_decode($data, true);
        if($data['result'] === true){
            return $data['data'];
        }
        return false;
    }

    public function fuwutuijian()
    {
        $url = "https://www.cifnews.com/collection/tiktok?origin=guoyuan_fuwutuijian";
        $data =  $this->GetHttp($url);

        $pos1 = strpos($data, "<div class=\"module__cont\"");
        $pos2 = strpos($data, "<div class=\"module__more\"");
        $detail = substr($data, $pos1 , $pos2 - $pos1 );

        $regex = '#href="([^"]+)"[^>]*>\s*([^<]+)</a>#is';
        $reg1 = "/<a.*?>*?<\/a>/";
        preg_match_all($reg1, $detail, $arr);


        preg_match_all("/<div class=\"cif-article__desc ellipsis([\S\s]+?)<\/div>/", $detail, $describes);

        $reg2 = "/<img .*?. alt.*?.>/";
        preg_match_all($reg2, $detail, $arrImage);

        $doc = new \DOMDocument();
        $data = [];
        foreach ($arr[0] as $key=>$value){
            preg_match_all($regex, $value,$mat);
            if(!empty($mat[1]) && !empty($mat[2])){
                $src = '';
                if(isset($arrImage[0][$key])){
                    $libxml_previous_state = libxml_use_internal_errors(true);
                    $doc->loadHTML($arrImage[0][$key]);
                    libxml_clear_errors();
                    $xpath = new \DOMXPath($doc);
                    libxml_use_internal_errors($libxml_previous_state);
                    $src = $xpath->evaluate("string(//img/@src)");
                }
                $describe = '';
                if(isset($describes[0][$key])){
                    $describe = $describes[0][$key];
                }
                $data[] = ['url'=>$mat[1][0], 'title'=>$mat[2][0], 'img'=> $src, 'describes'=>strip_tags( $describe)];

            }
        }
        return $data;
        exit();




        exit();

        preg_match_all($regex,$detail,$matches);

        $detail = preg_replace('#alt="[^"]*"#i', '', $detail);
/*        preg_match_all("/\<a.*?><img.*\>/U", $detail, $img);*/
        preg_match_all("/\<a.*?<\a>/U", $detail, $img);
//        $detail = explode("<div class=\"cif-article", $detail);

        var_dump($matches);
        var_dump($img);

    }

    /**
     * 快出海·
     */
    public function keygoogle()
    {
        $url = "https://www.kchuhai.com/report/keygoogle_pg1";
        $data = $this->GetHttp($url);
//
        $pos1 = strpos($data, "<div class=\"layui-tab-item layui-show\"");
        $pos2 = strpos($data, "<div class=\"kch-rightBox\"");
        $detail = substr($data, $pos1 , $pos2 - $pos1-20 );
        preg_match_all("/<div class=\"kch-information flex align-center justify-start kch-opacity border-bottom py-2([\S\s]+?)<\/div>/", $detail, $describes);
        echo "<pre>";
        $doc = new \DOMDocument();
        $reg1="/<a .*?>.*?<\/a>/";
        $reg2 = "/<div class=\"w-100 text-666 font-14 text-ellipsis2\"([\S\s]+?)<\/div>/";
        $reg3 = "/<div class=\"mb-1\"([\S\s]+?)<\/div>/";
        foreach ($describes[0] as $val){
            $libxml_previous_state = libxml_use_internal_errors(true);
            $doc->loadHTML($val);
            libxml_clear_errors();
            $xpath = new \DOMXPath($doc);
            libxml_use_internal_errors($libxml_previous_state);
            $src = $xpath->evaluate("string(//img/@src)");
            $href = $xpath->evaluate("string(//a/@href)");
            preg_match_all($reg2, $val, $describes);
            preg_match_all($reg1, $val,$title);
            var_dump($src);
            var_dump($href);
            var_dump($title[0][0]);
//            var_dump( strip_tags( $describes[0][0]));
            $desc = $this->GetHttp($href);


            preg_match_all($reg3, $desc,$desc2);
//            var_dump($desc2[0][0]);
            preg_match_all("/<img.*\>/U", $desc2[0][0], $img, PREG_PATTERN_ORDER);
//            var_dump($img[0]);

            foreach ($img[0] as $val2){

                $libxml_previous_state = libxml_use_internal_errors(true);
                $doc->loadHTML($val2);
                libxml_clear_errors();
                $xpath = new \DOMXPath($doc);
                libxml_use_internal_errors($libxml_previous_state);
                $src = $xpath->evaluate("string(//img/@src)");

                var_dump($val2);
                $data = $this->getRemoteFileToLocal($src, ROOT_PATH . 'public/uploads/reptile/');
                if($data['code'] == 1){
                    $AwsImgUrl = (new Upload())->fileUpload(ROOT_PATH.'public/uploads/reptile/'.$data['path']);
                    $str = str_replace($src, $AwsImgUrl, $val2);
                    $detail = str_replace($val2, $str, $desc2[0][0]);
                }else{
                    $detail = str_replace($val, '',  $desc2[0][0]);
                }
            }
            var_dump($detail);

            exit();
        }

    }

    /**
     * get
     * @param string $url 请求地址
     */
    public function GetHttp($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
        $tmpInfo = curl_exec($curl);
        if (curl_exec($curl) === false) {
            return 'Curl error: ' . curl_error($curl);
        }
        //关闭URL请求
        curl_close($curl);
        return $tmpInfo; //返回json对象
    }

    /**
     * 模拟post进行url请求
     * @param string $postUrl
     * @param string $param
     */
    private function request_post($postUrl = '', $param = '')
    {
        if (empty($postUrl) || empty($param)) {
            return false;
        }

        $curlPost = $param;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL, $postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);

        return $data;
    }

    /**
     * @param $remote_file  远程文件名
     * @param $local_dir   本地目录
     * 获取远程图片到本地
     */
    public function getRemoteFileToLocal($remote_file, $local_dir)
    {
        if (!is_dir($local_dir)) {
            $this->info['code'] = -1;
            $this->info['msg']  = '参数2只能是目录';
            return $this->info;
        }

        $fileinfo = pathinfo($remote_file);
        $ext = !empty($fileinfo['extension']) ? $fileinfo['extension'] : 'jpg';

        if (!in_array(strtolower($ext), ['jpg', 'jpeg', 'gif', 'png', 'bmp'])) {
            $this->info['code'] = -1;
            $this->info['msg']  = '图片格式不正确';
            return $this->info;
        }

        $filename = $local_dir .date('Y-m-d').DS .$fileinfo['filename'] . '.' . $ext;
        $this->mkDirs($local_dir .date('Y-m-d'));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $remote_file);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $result = curl_exec($ch);
        curl_close($ch);

        $resource = fopen($filename, 'w+');

        fwrite($resource, $result);
        fclose($resource);
        $this->info['code'] = 1;
        $this->info['msg']  = '图片下载成功';
        $this->info['path'] = date('Y-m-d').DS .$fileinfo['filename'] . '.' . $ext;
        return $this->info;
    }

    private function mkDirs($dir, $mode = 0777)
    {
        if (is_dir($dir) || @mkdir($dir, $mode)) return TRUE;
        if (!mkdirs(dirname($dir), $mode)) return FALSE;
        return @mkdir($dir, $mode);
    }

    /**
     * 功能：php完美实现下载远程图片保存到本地
     * 参数：文件url,保存文件目录,保存文件名称，使用的下载方式
     * 当保存文件名称为空时则使用远程文件原来的名称
     */
    public function getImage($url, $save_dir = '', $filename = '', $type = 0)
    {
        if(trim($url)==''){
            return array('file_name'=>'','save_path'=>'','error'=>1);
        }
        if(trim($save_dir)==''){
            $save_dir='./';
        }
        if(trim($filename)==''){//保存文件名
            $ext = strrchr($url, '.');
//            if($ext!='.gif'&&$ext!='.jpg'){
//                return array('file_name'=>'','save_path'=>'','error'=>3);
//            }
            $filename = time() . $ext;
        }
        if (0 !== strrpos($save_dir, '/')) {
            $save_dir .= '/';
        }
        //创建保存目录
        if (!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
            return array('file_name' => '', 'save_path' => '', 'error' => 5);
        }
        //获取远程文件所采用的方法
        if($type){
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $img = curl_exec($ch);
            curl_close($ch);
        }else{
            ob_start();
            readfile($url);
            $img=ob_get_contents();
            ob_end_clean();
        }
        $fp2 = @fopen($save_dir . $filename, 'a');
        fwrite($fp2,$img);
        fclose($fp2);
        unset($img, $url);
        return array('file_name' => $filename, 'save_path' => $save_dir . $filename, 'error' => 0);
    }

    public function get_microtime()
    {
        list($usec, $sec) = explode(' ', microtime());
        return ((float)$usec + (float)$sec);
    }

    /**
     * @param $arr
     * @param $str
     */
    public function strReplace($arr, &$str)
    {
        $arr[1] = $arr[1]=='空白' ? '' : $arr[1];
        $str = str_replace($arr[0], $arr[1], $str);
    }

}