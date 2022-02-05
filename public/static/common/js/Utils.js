
/*
工具类
*/
var Utils={
	/*成功的Toast*/
	'successToast':function(msg){
		layer.msg(msg, {
	      offset: '15px'
	      ,icon: 1
	    });
	},
	// 失败的toast
	'errorToast':function(msg){
		layer.msg(msg, {
	      offset: '15px'
	      ,icon: 2
	    });
	}
};