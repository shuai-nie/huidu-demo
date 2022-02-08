document.write("<script src='/static/common/js/HttpUtils.js'></script>");
/*
	网络请求路径
*/
var Api = {
    'login': function (data, callback) {
        return HttpUtils.post("login/login", data, function (res) {
            callback(res);
        });
    },
    'edit': function (data, callback) {
        return HttpUtils.post("", data, function (res) {
            if (res.code == 200) {
                layer.msg(res.msg, {icon: 1}, function(){
                    var index = parent.layer.getFrameIndex(window.name);
                    parent.layui.table.reload("table");
                    parent.layer.close(index);
                })
            }

            callback(res);
        }, 'json');
    },
    'add': function (data, callback) {
        return HttpUtils.post("", data, function (res) {
            if (res.code == 200) {
                layer.msg(res.msg, function(){
                    var index = parent.layer.getFrameIndex(window.name);
                    parent.layui.table.reload("table");
                    parent.layer.close(index);
                })
            }
            callback(res);
        });
    },
    "del": function (id, callback, url = 'delete') {
        return HttpUtils.get(url, {"id": id}, function (res) {
            callback(res);
        });
    }
};