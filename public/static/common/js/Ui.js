/*
	分页表格工具类
*/
var UI = {
    "getPageTable": function (table, url, cols, elem = "#think-table", height = "500") {
        table.render({
            elem: elem
            ,id : 'table'
            ,method:'post'
            , height: height
            , url: url //数据接口
            , limits : [10,20,50,100,200,500,1000,5000,10000]
            , page: true//开启分页
            , toolbar: '#toolbarDemo'
            , defaultToolbar: ['filter','exports']
            , parseData: function (res) {
                return {
                    "code": 0,
                    "msg": res['msg'],
                    "count": res['data']['count'],
                    "data": res['data']['list']
                };
            }
            , cols: [cols]

        });
    },
    "openLayer": function (url, x = 750, y = 600, confirmButton = "#submit", _title ="信息") {
        layer.open({
            type: 2,
            anim: 2,
            title:_title,
            area: [x + 'px', y + 'px'],
            btn: ['确定', '取消'],
            skin: 'demo-class',
            yes: function (index, layero) {
                //点击确认触发 iframe 内容中的按钮提交
                var submit = layero.find('iframe').contents().find(confirmButton);
                submit.click();
            },
            content: url,//这里content是一个URL，如果你不想让iframe出现滚动条，你还可以content: ['http://sentsin.com', 'no']
            end: function () {
                // think-table
            }
        });
    },
    "delete": function (id, callback, obj) {
        layer.confirm('是否确定删除？', {
			btn: ['确认', '取消'],
			success: function () {
				this.enterEsc = function (event) {
					if (event.keyCode === 13) {
						$(".layui-layer-btn0").click();
						return false; //阻止系统默认回车事件
					} else if (event.keyCode == 27) {
						$(".layui-layer-btn1").click();
						return false;
					}
				};
				$(document).on('keydown', this.enterEsc); //监听键盘事件，关闭层
			},
			end: function () {
				$(document).off('keydown', this.enterEsc); //解除键盘关闭事件
			}
		},
		function (index) {//点击确定后执行的方法体
			Api.del(id, function (res) {
				if (res['code'] == 200) {
                    layer.msg(res['msg'], {icon: 1}, function(){
                        layer.close(index);
                    });
				}else{
                    layer.msg(res['msg'], {icon: 5}, function(){
                        layer.close(index);
                    });
                }
				callback(res);
			});
		});

    }
};