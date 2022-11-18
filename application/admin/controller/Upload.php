<?php

namespace app\admin\controller;


use Aws\Credentials\Credentials;
use Aws\DynamoDb\DynamoDbClient;
use Aws\Exception\MultipartUploadException;
use Aws\Resource\Aws;
use Aws\S3\MultipartUploader;
use Aws\S3\ObjectUploader;
use Aws\S3\S3Client;
use Aws\Sts\StsClient;
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
                $getSaveName = $info->getSaveName();
                $aliyunConfig    = config('aliyun_config');
                $accessKeyId     = $aliyunConfig['accessKeyId'];
                $accessKeySecret = $aliyunConfig['accessKeySecret'];
                $endpoint        = $aliyunConfig['endpoint'];
                // 设置存储空间名称。
                $bucket = $aliyunConfig['bucket'];

                // 设置文件名称。
                $object = $info->getFilename();
                $filePath = ROOT_PATH . 'public/uploads/' . $getSaveName;

                try {
                    // Aws
                    $url = $this->fileUpload($filePath);
/*
                    Aliyun
                    $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
                    $data = $ossClient->uploadFile($bucket, $object, $filePath);
                    $url = $aliyunConfig['accessDomain'] . $object;
*/
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

    public function ueditor()
    {
        /**
         *  * array(
         *     "state" => "",          //上传状态，上传成功时必须返回"SUCCESS"
         *     "url" => "",            //返回的地址
         *     "title" => "",          //新文件名
         *     "original" => "",       //原始文件名
         *     "type" => ""            //文件类型
         *     "size" => "",           //文件大小
         * )
         */
        $action = request()->param('action');

        if($action == 'uploadimage') {
            $file = request()->file('file');
            if ($file) {
                $info = $file->validate(['size'=>30520000, 'ext'=>'jpg,png,gif,jpeg'])->move(ROOT_PATH . 'public' . DS . 'uploads');
                if($info) {
                    $getSaveName = $info->getSaveName();
                    $aliyunConfig    = config('aliyun_config');
                    $accessKeyId     = $aliyunConfig['accessKeyId'];
                    $accessKeySecret = $aliyunConfig['accessKeySecret'];
                    $endpoint        = $aliyunConfig['endpoint'];
                    // 设置存储空间名称。
                    $bucket = $aliyunConfig['bucket'];

                    $getFilename = $info->getFilename();

                    // 设置文件名称。
                    $object = $getFilename;
                    $filePath = ROOT_PATH . 'public/uploads/' . $getSaveName;

                    try {
// Aws
                        $url = $this->fileUpload($filePath);
//                        // Alioss
//                        $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
//                        $data = $ossClient->uploadFile($bucket, $object, $filePath);
//                        $url = $aliyunConfig['accessDomain'] . $object ;

                        return json([
                            'original'        => $getFilename,
                            'size'            => $info->getSize(),
                            'state'           => "SUCCESS",
                            'title'           => $getFilename,
                            'type'            => "." . $info->getExtension(),
                            'url'             => $url,
                            'imageActionName' => 'uploadimage',
                        ], 200);
                    } catch (OssException $e) {
                        return json($e, 200);
                    }
                }
            }

        }elseif ($action == 'config') {
            return json([
                /* 前后端通信相关的配置,注释只允许使用多行方式 */

                /* 上传图片配置项 */
                "imageActionName"     => "uploadimage", /* 执行上传图片的action名称 */
                "imageFieldName"      => "file", /* 提交的图片表单名称 */
                "imageMaxSize"        => 30520000, /* 上传大小限制，单位B */
                "imageAllowFiles"     => [".png", ".jpg", ".jpeg", ".gif", ".bmp"], /* 上传图片格式显示 */
                "imageCompressEnable" => true, /* 是否压缩图片,默认是true */
                "imageCompressBorder" => 500, /* 图片压缩最长边限制 */
                "imageInsertAlign"    => "none", /* 插入的图片浮动方式 */
                "imageUrlPrefix"      => "", /* 图片访问路径前缀 */
                "imagePathFormat"     => "/ueditor/php/upload/image/{yyyy}{mm}{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
                /* {filename} 会替换成原文件名,配置这项需要注意中文乱码问题 */
                /* {rand:6} 会替换成随机数,后面的数字是随机数的位数 */
                /* {time} 会替换成时间戳 */
                /* {yyyy} 会替换成四位年份 */
                /* {yy} 会替换成两位年份 */
                /* {mm} 会替换成两位月份 */
                /* {dd} 会替换成两位日期 */
                /* {hh} 会替换成两位小时 */
                /* {ii} 会替换成两位分钟 */
                /* {ss} 会替换成两位秒 */
                /* 非法字符 \ : * ? " < > | */
                /* 具请体看线上文档: fex.baidu.com/ueditor/#use-format_upload_filename */

                /* 涂鸦图片上传配置项 */
                "scrawlActionName"  => "uploadscrawl",      /* 执行上传涂鸦的action名称 */
                "scrawlFieldName"   => "upfile",            /* 提交的图片表单名称 */
                "scrawlPathFormat"  => "/ueditor/php/upload/image/{yyyy}{mm}{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
                "scrawlMaxSize"     => 1048576, /* 上传大小限制，单位B */
                "scrawlUrlPrefix"   => "", /* 图片访问路径前缀 */
                "scrawlInsertAlign" => "none",

                /* 截图工具上传 */
                "snapscreenActionName"  => "uploadimage", /* 执行上传截图的action名称 */
                "snapscreenPathFormat"  => "/ueditor/php/upload/image/{yyyy}{mm}{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
                "snapscreenUrlPrefix"   => "", /* 图片访问路径前缀 */
                "snapscreenInsertAlign" => "none", /* 插入的图片浮动方式 */

                /* 抓取远程图片配置 */
                "catcherLocalDomain" => ["127.0.0.1", "localhost", "img.baidu.com"],
                "catcherActionName"  => "catchimage", /* 执行抓取远程图片的action名称 */
                "catcherFieldName"   => "source", /* 提交的图片列表表单名称 */
                "catcherPathFormat"  => "/ueditor/php/upload/image/{yyyy}{mm}{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
                "catcherUrlPrefix"   => "", /* 图片访问路径前缀 */
                "catcherMaxSize"     => 1048576, /* 上传大小限制，单位B */
                "catcherAllowFiles"  => [".png", ".jpg", ".jpeg", ".gif", ".bmp"], /* 抓取图片格式显示 */

                /* 上传视频配置 */
                "videoActionName" => "uploadvideo", /* 执行上传视频的action名称 */
                "videoFieldName"  => "upfile", /* 提交的视频表单名称 */
                "videoPathFormat" => "/ueditor/php/upload/video/{yyyy}{mm}{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
                "videoUrlPrefix"  => "", /* 视频访问路径前缀 */
                "videoMaxSize"    => 102400000, /* 上传大小限制，单位B，默认100MB */
                "videoAllowFiles" => [
                            ".flv", ".swf", ".mkv", ".avi", ".rm", ".rmvb", ".mpeg", ".mpg",
                            ".ogg", ".ogv", ".mov", ".wmv", ".mp4", ".webm", ".mp3", ".wav", ".mid"], /* 上传视频格式显示 */

                /* 上传文件配置 */
                "fileActionName" => "uploadfile", /* controller里,执行上传视频的action名称 */
                "fileFieldName"  => "upfile", /* 提交的文件表单名称 */
                "filePathFormat" => "/ueditor/php/upload/file/{yyyy}{mm}{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
                "fileUrlPrefix"  => "", /* 文件访问路径前缀 */
                "fileMaxSize"    => 1048576, /* 上传大小限制，单位B，默认50MB */
                "fileAllowFiles" => [
                            ".png", ".jpg", ".jpeg", ".gif", ".bmp",
                            ".flv", ".swf", ".mkv", ".avi", ".rm", ".rmvb", ".mpeg", ".mpg",
                            ".ogg", ".ogv", ".mov", ".wmv", ".mp4", ".webm", ".mp3", ".wav", ".mid",
                            ".rar", ".zip", ".tar", ".gz", ".7z", ".bz2", ".cab", ".iso",
                            ".doc", ".docx", ".xls", ".xlsx", ".ppt", ".pptx", ".pdf", ".txt", ".md", ".xml"
                        ], /* 上传文件格式显示 */

                /* 列出指定目录下的图片 */
                "imageManagerActionName"  => "listimage", /* 执行图片管理的action名称 */
                "imageManagerListPath"    => "/ueditor/php/upload/image/", /* 指定要列出图片的目录 */
                "imageManagerListSize"    => 20, /* 每次列出文件数量 */
                "imageManagerUrlPrefix"   => "", /* 图片访问路径前缀 */
                "imageManagerInsertAlign" => "none", /* 插入的图片浮动方式 */
                "imageManagerAllowFiles"  => [".png", ".jpg", ".jpeg", ".gif", ".bmp"], /* 列出的文件类型 */

                /* 列出指定目录下的文件 */
                "fileManagerActionName" => "listfile", /* 执行文件管理的action名称 */
                "fileManagerListPath"   => "/ueditor/php/upload/file/", /* 指定要列出文件的目录 */
                "fileManagerUrlPrefix"  => "", /* 文件访问路径前缀 */
                "fileManagerListSize"   => 20, /* 每次列出文件数量 */
                "fileManagerAllowFiles" => [
                        ".png", ".jpg", ".jpeg", ".gif", ".bmp",
                        ".flv", ".swf", ".mkv", ".avi", ".rm", ".rmvb", ".mpeg", ".mpg",
                        ".ogg", ".ogv", ".mov", ".wmv", ".mp4", ".webm", ".mp3", ".wav", ".mid",
                        ".rar", ".zip", ".tar", ".gz", ".7z", ".bz2", ".cab", ".iso",
                        ".doc", ".docx", ".xls", ".xlsx", ".ppt", ".pptx", ".pdf", ".txt", ".md", ".xml"
                    ] /* 列出的文件类型 */
            ]);
        }


    }

    public function dies()
    {
        $this->fileUpload('123.png');

    }

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
