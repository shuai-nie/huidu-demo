# tp5.1+layui通用后台模板

#### Description
用于快速搭建后台,通用crud 易于管理


#### Installation

1. 安装数据库文件 admin.sql
2. 修改database.php 库名 密码 等


#### 使用包

1. 百度富文本编辑器
2. [easypay](https://gitee.com/yansongda/pay) 微信阿里支付
3. [layui](https://www.layuion.com/doc/)后台模板
4. 核心框架[THINKPHP](https://www.kancloud.cn/manual/thinkphp5_1) 5.1


```nginx
server {
	listen       80;
	server_name  thinkcms.nf;
#	root   "D:/WWW/code.aliyun.com/thinkcms/public";
	root   "D:/WWW/code.aliyun.com/boniu-resource/public";
	location / {
		index index.html index.php;
		#try_files $uri $uri/ /index.php$is_args$args;
		#autoindex  on;
		 if (!-e $request_filename)
             {
                rewrite ^/(.*)$ /index.php?s=$1;
             }
	}
	
	location ~ \.php(.*)$ {
		fastcgi_pass   127.0.0.1:9000;
		fastcgi_index  index.php;
		fastcgi_split_path_info  ^((?U).+\.php)(/?.+)$;
		fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
		fastcgi_param  PATH_INFO  $fastcgi_path_info;
		fastcgi_param  PATH_TRANSLATED  $document_root$fastcgi_path_info;
		include        fastcgi_params;
	}
}
```



