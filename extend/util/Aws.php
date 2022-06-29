<?php


namespace util;


use Aws\Credentials\Credentials;
use Aws\S3\MultipartUploader;
use Aws\S3\S3Client;

class Aws
{

    /**
     * 分段上传图片
     * @param $source
     * @return string
     * @author Lucius yesheng35@126.com
     */
    public function fileUpload($source)
    {
        //设置超时
        set_time_limit(0);
        $aws = config('Aws');
        $s3Key = $aws['AccessKeyId']; //"AKIAZC2SXKMXFQO6YQTR";
        $s3Secret = $aws['SecretAccessKey']; //"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";
        $bucket = $aws['Bucket']; //"huidu-bucket";
        $ENDPOINT = $aws['Endpoint']; //"https://s3.ap-southeast-1.amazonaws.com/";
        $credentials = new Credentials($s3Key, $s3Secret);
        $s3Client = new S3Client([
            'region' => 'ap-southeast-1',
            'version' => 'latest',
            'endpoint' => $ENDPOINT,
            'credentials' =>$credentials
        ]);
        // $source = fopen( ROOT_PATH . 'public/uploads/20220613/'.$file, 'rb');
        $key = "huidu/images/" . date('Y-m-d') . '/' . date('YmdHis') . '.png';
        $uploader = new MultipartUploader($s3Client, $source, [
            'bucket' => $bucket,
            'key' => $key,
            "ContentType" => 'image/png',
            'before_initiate' => function (\Aws\Command $command) {
                // $command is a CreateMultipartUpload operation
                $command['CacheControl'] = 'max-age=3600';
            },
            'before_upload' => function (\Aws\Command $command) {
                // $command is an UploadPart operation
                $command['RequestPayer'] = 'requester';
            },
            'before_complete' => function (\Aws\Command $command) {
                // $command is a CompleteMultipartUpload operation
                $command['RequestPayer'] = 'requester';
            },
        ]);

        $result = $uploader->upload();

        $publicUrl = $s3Client->getObjectUrl($bucket, $key);

        /*或者为私人内容生成签名网址：
        $validTime = '+10 minutes';
        $signedUrl = $s3Client->getObjectUrl($bucket, $keyname, $validTime);*/

        return $publicUrl;
    }
}