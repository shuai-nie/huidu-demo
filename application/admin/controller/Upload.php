<?php

namespace app\admin\controller;

use OSS\Core\OssException;
use think\Controller;
use think\Request;
use OSS\OssClient;

class Upload extends Controller
{
    public function upload()
    {
        $file = \request()->file('file');
        if ($file) {
            $info = $file->validate(['size'=>30520000, 'ext'=>'jpg,png,gif,jpeg'])->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info) {

                $getSaveName = $info->getSaveName() ;

                $aliyunConfig = config('aliyun_config');
                $accessKeyId = $aliyunConfig['accessKeyId'];
                $accessKeySecret = $aliyunConfig['accessKeySecret'];
                $endpoint = $aliyunConfig['endpoint'];
                // 设置存储空间名称。
                $bucket = $aliyunConfig['bucket'];

                // 设置文件名称。
                $object = 'boniu_ziyuanku/' .$info->getFilename();
                $filePath = ROOT_PATH . 'public/uploads/' . $getSaveName;

                try {
                    $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
                    $data = $ossClient->uploadFile($bucket, $object, $filePath);
                    $url = $aliyunConfig['accessDomain'] . $object;
                    return json([
                        'code' => 0,
                        'url' => $url,
                    ]);
                } catch (OssException $e) {

                }



            } else {
                $file->getError();
            }
        }
    }
}
