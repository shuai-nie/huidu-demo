document.write("<script src='/static/common/js/jquery.js'></script>");
/*
	网络相关工具类
*/
var HttpUtils={
	'post':function(url,data,callback){
		$.ajax({
		type:"POST",	//请求的类型,GET、POST等	
		url:url,	//向服务器请求的地址。
		//向服务器发送内容的类型，默认值是：application/x-www-form-urlencoded
		dataType:'JSON',	//预期服务器响应类型
		data:data,
		async:true,	//默认值是true,表示请求是异步的，false是同步请求，同步请求会阻碍浏览器的其他操作（不建议使用）
		timeout:'5000',	//设置本地的请求超时时间（单位是毫秒）
		cache:true,	//设置浏览器是否缓存请求的页面
		success:function(result,status,XMLHttpRequest){		//请求成功是执行的函数,result：服务器返回的数据，    status：服务器返回的状态，
                callback(result);
                },
                error:function(xhr,status,error){	//请求失败是执行的函数
                	console.log("失败了");
                }
           })
	},
	'get':function(url,data,callback){
		$.ajax({
		type:"GET",	//请求的类型,GET、POST等	
		url:url,	//向服务器请求的地址。
		contentType:'application/json',	//向服务器发送内容的类型，默认值是：application/x-www-form-urlencoded
		dataType:'JSON',	//预期服务器响应类型
		data:data,
		async:true,	//默认值是true,表示请求是异步的，false是同步请求，同步请求会阻碍浏览器的其他操作（不建议使用）
		timeout:'5000',	//设置本地的请求超时时间（单位是毫秒）
		cache:true,	//设置浏览器是否缓存请求的页面
		success:function(result,status,XMLHttpRequest){		//请求成功是执行的函数,result：服务器返回的数据，    status：服务器返回的状态，
                callback(result);
                },
                error:function(xhr,status,error){	//请求失败是执行的函数
                	console.log("失败了");
                }
           })
	}
};