/** layuiAdmin.std-v1.0.0 LPPL License By http://www.layui.com/admin/ */
;
layui.define(function(e) {
	// 折线图数据
	var month;
	var monthMoney;
	// 饼状图数据
	
    layui.use(["admin", "carousel"],
    function() {
        var e = layui.$,
        t = (layui.admin, layui.carousel),
        a = layui.element,
       
        i = layui.device();

        
        e(".layadmin-carousel").each(function() {
            var a = e(this);
            t.render({
                elem: this,
                width: "100%",
                arrow: "none",
                interval: a.data("interval"),
                autoplay: a.data("autoplay") === !0,
                trigger: i.ios || i.android ? "click": "hover",
                anim: a.data("anim")
            })
        }),
        a.render("progress")
    }),
    layui.use(["carousel", "echarts"],
    function() {
    	var $=layui.jquery
    	$.post("/Index/Index/main","",function(res){
        	month=res.zhexiantu.month;
        	monthMoney=res.zhexiantu.money;
        	var pie=res.bing;
            var leixing=[];
            for(var i=0;i<pie.length;i++){
                leixing.push(pie[i]['name']);
            }
            console.log(leixing);
        	console.log(res.bing);
        	var e = layui.$,
        t = layui.carousel,

        a = layui.echarts,
        i = [],
        l = [{
            title: {
                text: "经营情况",
                x: "center",
                textStyle: {
                    fontSize: 14
                }
            },
            tooltip: {
                trigger: "axis"
            },
            legend: {
                data: ["", ""]
            },
            xAxis: [{
                type: "category",
                boundaryGap: !1,
                data: month
            }],
            yAxis: [{
                type: "value"
            }],
            series: [{
                name: "PV",
                type: "line",
                smooth: !0,
                itemStyle: {
                    normal: {
                        areaStyle: {
                            type: "default"
                        }
                    }
                },
                data: monthMoney
            }]
        },
        {
            title: {
                text: "企业类型",
                x: "center",
                textStyle: {
                    fontSize: 14
                }
            },
            tooltip: {
                trigger: "item",
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            legend: {
                orient: "vertical",
                x: "left",
                data: leixing
            },
            series: [{
                name: "企业类型",
                type: "pie",
                radius: "55%",
                center: ["50%", "50%"],
                data: pie
            }]
        }],
        n = e("#LAY-index-dataview").children("div"),
        r = function(e) {
            i[e] = a.init(n[e], layui.echartsTheme),
            i[e].setOption(l[e]),
            window.onresize = i[e].resize
        };
        if (n[0]) {
            r(0);
            var o = 0;
            t.on("change(LAY-index-dataview)",
            function(e) {
                r(o = e.index)
            }),
            layui.admin.on("side",
            function() {
                setTimeout(function() {
                    r(o)
                },
                300)
            }),
            layui.admin.on("hash(tab)",
            function() {
                layui.router().path.join("") || r(o)
            })
        }
        });
        
    }),
    e("console", {})
});