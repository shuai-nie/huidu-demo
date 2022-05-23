<?php


namespace util;
use OSS\Core\OssException;
use OSS\OssClient;

class AliyunOssClient
{

    public function OssClient($object, $filePath)
    {
        $aliyunConfig    = config('aliyun_config');
        $accessKeyId     = $aliyunConfig['accessKeyId'];
        $accessKeySecret = $aliyunConfig['accessKeySecret'];
        $endpoint        = $aliyunConfig['endpoint'];
        // 设置存储空间名称。
        $bucket = $aliyunConfig['bucket'];

        try {
            $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
            return $ossClient->uploadFile($bucket, $object, $filePath);
        } catch (\Exception $e) {
            return false;
        }
    }
}