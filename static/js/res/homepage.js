layui.define
(
    function (exports) {

        var defaultLoadEquipId = 0;

//页面初始化
        function initPage() {
            layui.use(["element", "table",  "carousel", "echarts"], function () {
                var element = layui.element
                element.init();
                layui.jquery(".layadmin-carousel").each
                (
                    function () {
                        (layui.carousel).render({
                            elem: this,
                            width: "100%",
                            arrow: "none",
                            interval: layui.jquery(this).data("interval"),
                            autoplay: layui.jquery(this).data("autoplay") === !0,
                            trigger: layui.device().ios || layui.device().android ? "click" : "hover",
                            anim: layui.jquery(this).data("anim")
                        })
                    }
                );
                item_tendency();
            });
        }
        //设备数量趋势
        function item_tendency() {
            layui.use(["carousel", "echarts"], function () {
                layui.jquery.ajax({
                    url: '/api/repair/item_tendency',
                    dataType: 'json'
                }).done(function (res) {
                    option = {
                        title: { subtext: "单位：台件"},
                        tooltip: {trigger: "axis"},
                        legend: {data: ["新增台数"]},
                        calculable: !0,
                        xAxis: [{
                            type: "category",
                            data: res.xAxis//["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"]
                        }],
                        yAxis: [{type: "value"}],
                        series:
                            [
                                {
                                    name: "设备新增数量", type: "line",
                                    data: res.series,// [2.6, 5.9, 9, 26.4, 28.7, 70.7, 175.6, 182.2, 48.7, 18.8, 6, 2.3],

                                }
                            ]
                    };

                    dom = layui.jquery("#LAY-index-dataview").children("div");
                    myChart = (layui.carousel, layui.echarts).init(dom[0], layui.echartsTheme);
                    myChart.setOption(option, true);
                    window.onresize = myChart.resize;

                });
            });
        }

        initPage();
        exports('homepage', this);
    }
);





