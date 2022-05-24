<?php
namespace util;

class UploadDownload
{

    public function download($url, $save_dir='', $filename='', $type=0)
    {
        if(trim($url) == '') {
            return array('file_name'=>'', 'save_path'=>'', 'error'=>1);
        }
        if(trim($save_dir) == '') {
            $save_dir = './img';
        }
        if(trim($filename) == '') {
            $ext = strrchr($url, '.');
            /*if($ext != '.git' && $ext != '.jpeg') {
                return array('file_name' => '', 'save_path' => '', 'error' => 3);
            }*/
            if($ext !== '') {
                $ext = '.png';
            }
            $filename =  date("YmdHis").mt_rand(10000, 99999) .$ext;
        }
        if (0 != strrpos($save_dir, '/')) {
            $save_dir .= '/';
        }
        if(!file_exists($save_dir) && !mkdir($save_dir, 0777, true)) {
            return array('file_name' => '', 'save_name' => '', 'error' => 5);
        }
        if($type) {
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $img = curl_exec($ch);
            curl_close($ch);
        } else {
            ob_start();
            readfile($url);
            $img = ob_get_contents();
            ob_end_clean();
        }

        $fp2 = @fopen($save_dir . $filename, 'a');
        fwrite($fp2, $img);
        fclose($fp2);
        unset($img, $url);
        return array('file_name' => $filename, 'save_name' => $save_dir . $filename, 'error' => 0);
    }

    public function replaceImg($content)
    {
        preg_match_all('/<img.*?src=["|\']?(.*?)["|\']?\s.*?>/i', $content, $match);
        $AliyunOssClient = new AliyunOssClient();
        $YmdPath = 'uploads/' . date('Ymd');
        $aliyun_config = Config('aliyun_config');//['accessDomain']);
        foreach ($match[1] as $value) {
            if(strpos($value, 'file.huidu.io') == false) {
                $data = $this->download($value, './' . $YmdPath);
                if($data['error'] == 0) {
                    $AliyunOssClient->OssClient($data['file_name'], ROOT_PATH . 'public/' . $YmdPath.'/' . $data['file_name']);
                    $content = str_replace($value, $aliyun_config['accessDomain']. $data['file_name'], $content);
                }
            }
        }
        return $content;
    }

}