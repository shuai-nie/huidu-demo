<?php
namespace util;

class Excel
{

    public function putCsv($head, $mark = 'attack_ip_info', $fileName='test.csv')
    {
        set_time_limit(0);
        $sqlCount = model('log')->count();
        header('Content-Type: application/vnd.ms-excel;charset=utf-8');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        //每次只从数据库取100000条以防变量缓存太大
        $sqlLimit = 100000;
        //每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
        $limit = 100000;
        //buffer计数器
        $cnt = 0;
        $fileNameArr = array();
        //逐行取出数据，不浪费内存
        for ($i = 0; $i < ceil($sqlCount / $sqlLimit); $i++) {
            //生成临时文件
            $fp = fopen($mark."_".$i.".csv", 'w');
            //修改可执行权限
            chmod($mark."_".$i.".csv", 777);
            $fileNameArr[] = $mark."_".$i.".csv";
            //将数据通过fputcsv写到文件句柄
            fputcsv($fp, $head);
            $dataArr = model('log')->field('id,uid')->limit($i * $sqlLimit,$sqlLimit)->select();
            foreach ($dataArr as $a) {
                $cnt++;
                if ($limit == $cnt) {
                    //刷新一下输出buffer，防止由于数据过多造成问题
                    ob_flush();
                    flush();
                    $cnt = 0;
                }
                fputcsv($fp, $a);
            }
            fclose($fp);//每生成一个文件关闭
        }

        //进行多个文件压缩
        $zip = new ZipArchive();
        $filename = $mark . ".zip";
        $zip->open($filename, ZipArchive::CREATE);//打开压缩包
        foreach ($fileNameArr as $file) {
            $zip->addFile($file, basename($file));//向压缩包中添加文件
        }
        $zip->close();//关闭压缩包
        foreach ($fileNameArr as $file) {
            unlink($file);//删除csv临时文件
        }
        //输出压缩文件提供下载
        header("Cache-Control: max-age=0");
        header("Content-Description: File Transfer");
        header('Content-disposition: attachment; filename=' . basename($filename));
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: binary");
        header('Content-Length: ' . filesize($filename));
        @readfile($filename);//输出文件;
        unlink($filename); //删除压缩包临时文件
    }

}