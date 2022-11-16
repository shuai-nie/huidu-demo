<?php

namespace lib;

use lib\Reptile as ApiReptile;

class Reptile
{
    // Facebook 海外营销列表
    public function apiCifNewsBrandFacebook()
    {
        $url = "https://www.cifnews.com/guoyuan/api/brand/facebook/all?size=10&key=facebook&code=all";
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
        preg_match_all("/\<img.*\>/U", $detail, $img);

        $doc = new \DOMDocument();
        foreach ($img[0] as $val){
            $str = $val;
            $str = str_replace( "src=\"https://pic.cifnews.com/upload/202103/04/202103041710135519.jpg!/both/750x386\"", "", $str);
            $str = str_replace( "data-src=\"", "src=\"", $str);
            $libxml_previous_state = libxml_use_internal_errors(true);
            $doc->loadHTML($str);
            libxml_clear_errors();
            $xpath = new \DOMXPath($doc);
            libxml_use_internal_errors($libxml_previous_state);
            $src = $xpath->evaluate("string(//img/@src)");

            $data = $this->getRemoteFileToLocal($src, ROOT_PATH . 'public/uploads/reptile/');
            if($data['code'] == 1){
                $str = str_replace($src, $data['path'], $str);
                $detail = str_replace($val, $str, $detail);
            }else{
                $detail = str_replace($val, '', $detail);
            }
        }
        $detail = str_replace('', '', $detail);
        // 去掉链接
        $detail = preg_replace("/<a[^>]*>(.*?)<\/a>/is", "$1", $detail);
        $detail .= "</div>";
        return $detail;
    }


    /**
     * get
     * @param string $url 请求地址
     */
    private function GetHttp($url)
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
        $this->info['path'] = 'http://img.91po.net/'.date('Y-m-d').DS .$fileinfo['filename'] . '.' . $ext;
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
        $str = str_replace($arr[0], $arr[1], $str);
    }

}