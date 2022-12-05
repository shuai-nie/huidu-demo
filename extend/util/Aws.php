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
        $s3Key = $aws['AccessKeyId'];
        $s3Secret = $aws['SecretAccessKey'];
        $bucket = $aws['Bucket'];
        $ENDPOINT = $aws['Endpoint'];
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

    public function ff($image_name, $filePath)
    {
        $s3Clinet = S3Client::factory();
        $aws = config('Aws');
        $bucket = $aws['Bucket'];
        $keyName = $image_name;
        $localFilePath = $filePath;
        $result = $s3Clinet->putObject(array(
            "Bucket" => $bucket,
            "Key" => $keyName,
            "SourceFile" => $filePath,
            "ACL" => "public-read",
            "ContentType" => "image/jpeg"

        ));
        unlink($localFilePath);
    }

    public function dd()
    {
        $s3Client = S3Client::factory();
        $bucket = 'my_s3_bucket';
        $keyname = $_POST['cat'] . '/original_' . uniqid('fu_') . '.jpg';
        $dataFromFile = file_get_contents($_FILES['uploadedfile']['tmp_name']);
        $result = $s3Client->putObject(array(
            'Bucket' => $bucket,
            'Key' => $keyname,
            'Body' => $dataFromFile,
            'ACL' => 'public-read',
        ));

        /*
        // 如果你有公共访问权限,可以获得s3链接
        $publicUrl = $s3Client->getObjectUrl($bucket, $keyname);
        // 或者为私人内容生成签名网址：
        $validTime = '+10 minutes';
        $signedUrl = $s3Client->getObjectUrl($bucket, $keyname, $validTime);
        */
    }

    public function AWS_S3Client(){
        $ACCESS_KEY_ID = '你的s3 ID';
        $SECRET_ACCESS_KEY = '你的s3 秘钥';
        $credentials = new Aws\Credentials\Credentials($ACCESS_KEY_ID, $SECRET_ACCESS_KEY);
        return newAws\S3\S3Client([
            'version' => 'latest',
            "region" => 'eu-central-1',//节点
            'credentials' => $credentials,
            //‘debug‘ => true
        ]);

    }

    /**
     * AWS S3上传文件
     * @param string $file 文件相对路径
     * @param string $fileName 上传后的文件名称
     * @param int $type 1使用断点续传，0不使用
     * @param bool $publicRead 是否开放访问
     * @return array $path
     */
    public function S3FileUpload($file = ‘‘, $fileName = ‘‘, $type = 0, $publicRead = false)
    {
        $s3Client = AWS_S3Client();
        $bucket = 'yourBucketName';//你的存储桶名称
        $source = FILE_UPLOAD . $file;//$source 需要绝对路径 注意更换成自己的目录配置
        $fileName = $fileName ? $fileName : $file;
        $config =[
            'bucket' => $bucket,
            'key' => $fileName,//这里如果是相对路径 如 test/img/1.png 会自动创建目录 如果是绝对路径则直接上传到指定的存储桶中
        ];//是否开放访问

        if ($publicRead) {
            $config['ACL'] = 'public-read';
        }
        $uploader = new Aws\S3\MultipartUploader($s3Client, $source, $config);
        $code = 0;
        $message = '';
        if ($type == 1) {
            //在分段上传过程中发生错误,重新开始未完成的上传。

            do{
                try{
                    $result = $uploader->upload();
                }catch (Aws\Exception\MultipartUploadException $e) {
                    $uploader = new Aws\S3\MultipartUploader($s3Client, $source, [ 'state' => $e->getState(),]);
                }

            }while (!isset($result));
            $code = 1;
            $message = urldecode($result['ObjectURL']);
        }else{
            try{
                $result = $uploader->upload();
                $code = 1;
                $message = urldecode($result['ObjectURL']);

            }catch (Aws\Exception\MultipartUploadException $e) {
                $message = $e->getMessage();
            }

        }
        return [‘code‘ => $code, ‘message‘ => $message];
    }

    /**
     * 生成AWS S3下载文件url地址
     * @param string $file 文件相对地址 如:test/img/1.png
     * @param string $expires 授权时间
     * @return string
     */
    public function S3FileDownload($file, $expires = '+10 minutes')
    {
        $s3Client =AWS_S3Client();
        $cmd = $s3Client->getCommand('GetObject', [
            'Bucket' => '你的存储桶名称',
            'Key' => $file //相对地址
        ]);
        $request = $s3Client->createPresignedRequest($cmd, $expires);//创建预签名 URL
        $presignedUrl = (string)$request->getUri();
        return $presignedUrl;
    }

}