/** layuiAdmin.std-v1.0.0 LPPL License By http://www.layui.com/admin/ */


layui.define
(
    function (exports) {

        layui.use('element', function () {

        });


        layui.use(["element", "table", "admin", "carousel", "echarts"], function () {

            layui.jquery(".layadmin-carousel").each
            (
                function () {
                    (layui.admin, layui.carousel).render({
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
            layui.element.render("progress")

            layui.jquery.ajax({
                url: '/api/echarts/revenue',
                dataType: 'json',
            }).done(function (res) {
                layui.jquery('#year-total-revenue').text(res.year);
                layui.jquery('#month-total-revenue').text(res.month);
            });

            layui.jquery.ajax({
                url: '/api/echarts/cost',
                dataType: 'json',
            }).done(function (res) {
                layui.jquery('#year-total-cost').text(res.year);
                layui.jquery('#month-total-cost').text(res.month);
            });

            layui.jquery.ajax({
                url: '/api/echarts/inspection',
                dataType: 'json',
            }).done(function (res) {
                layui.jquery('#year-total-inspection').text(res.year);
                layui.jquery('#month-total-inspection').text(res.month);
            });

            layui.jquery.ajax({
                url: '/api/echarts/return_rate',
                dataType: 'json',
            }).done(function (res) {
                layui.jquery('#year-return-rate').text(res.year);
                layui.jquery('#last-year-return-rate').text(res.lastYear);
            });


            layui.table.render({
                elem: '#varcost'
                , url: '/api/echarts/varcost/'
                , cellMinWidth: 80 //全局定义常规单元格的最小宽度，layui 2.2.1 新增
                , cols: [[
                    {field: 'id', width: 80, title: 'ID', sort: true}
                    , {field: 'equip_id', title: '设备ID'}
                    , {field: 'type', title: '费用类型'}
                    , {field: 'start_dt', title: '计费开始时间'}
                    , {field: 'end_dt', title: '计费开始时间'}
                    , {field: 'amount', title: '金额', sort: true}
                ]]
            });
            layui.table.render({
                elem: '#fixcost'
                , url: '/api/echarts/fixcost/'
                , cellMinWidth: 80 //全局定义常规单元格的最小宽度，layui 2.2.1 新增
                , cols: [[
                    {field: 'id', width: 80, title: 'ID', sort: true}
                    , {field: 'equip_id', title: '设备ID'}
                    , {field: 'type', title: '费用类型'}
                    , {field: 'start_dt', title: '计费开始时间'}
                    , {field: 'end_dt', title: '计费开始时间'}
                    , {field: 'amount', title: '金额', sort: true}
                ]]
            });
            layui.table.render({
                elem: '#dept'
                , url: '/api/echarts/dept/'
                , cellMinWidth: 80 //全局定义常规单元格的最小宽度，layui 2.2.1 新增
                , cols: [[
                    {field: 'id', width: 80, title: 'ID', sort: true}
                    , {field: 'equip_id', title: '设备ID'}
                    , {field: 'dept_name', title: '科室名称'}
                    , {field: 'count', title: '本月检查人次', sort: true}
                    , {field: 'profit', title: '本月收入', sort: true}
                    , {field: 'cost', title: '本月成本', sort: true}
                ]]
            });
            layui.table.render({
                elem: '#source'
                , url: '/api/echarts/source/'
                , cellMinWidth: 80 //全局定义常规单元格的最小宽度，layui 2.2.1 新增
                , cols: [[
                    {field: 'id', width: 80, title: 'ID', sort: true}
                    , {field: 'equip_id', title: '设备ID'}
                    , {field: 'dept_name', title: '检查来源'}
                    , {field: 'count', title: '本月检查人次'}
                    , {field: 'profit', title: '本月收入'}
                    , {field: 'cost', title: '本月成本', sort: true}
                ]]
            });

            layui.jquery.ajax({
                url: '/api/echarts/benefit',
                dataType: 'json',
            }).done(function (res) {

                maxValue = Math.max.apply(null, res.series.income);
                minValue = Math.min.apply(null, res.series.income);

                avgRevenueValue = Math.round(res.series.income.reduce((a, b) => a + b) / res.series.income.length);
                avgCostValue = Math.round(res.series.cost.reduce((a, b) => a + b) / res.series.cost.length);

                layui.jquery('#month-avg-revenue').text(avgRevenueValue);
                layui.jquery('#month-avg-cost').text(avgCostValue);

                option = {
                    title: {text: "设备效益趋势分析", subtext: "单位：万元"},
                    tooltip: {trigger: "axis"},
                    legend: {data: ["成本", "收入"]},
                    calculable: !0,
                    xAxis: [{
                        type: "category",
                        data: res.xAxis//["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"]
                    }],
                    yAxis: [{type: "value"}],
                    series:
                        [
                            {
                                name: "成本", type: "bar",
                                data: res.series.cost,
                                markPoint: {data: [{type: "max", name: "最大值"}, {type: "min", name: "最小值"}]},
                                markLine: {data: [{type: "average", name: "平均值"}]}
                            },
                            {
                                name: "收入", type: "bar",
                                data: res.series.income,//[2.6, 5.9, 9, 26.4, 28.7, 70.7, 175.6, 182.2, 48.7, 18.8, 6, 2.3],
                                markPoint: {
                                    data: [
                                        {
                                            name: "年最高",
                                            value: maxValue,
                                            xAxis: res.series.income.length,
                                            yAxis: maxValue,
                                            symbolSize: 18
                                        },
                                        {
                                            name: "年最低",
                                            value: minValue,
                                            xAxis: res.series.income.length,
                                            yAxis: minValue
                                        }
                                    ]
                                },
                                markLine: {data: [{type: "average", name: "平均值"}]}
                            }
                        ]
                };

                dom = layui.jquery("#LAY-index-pageone").children("div");
                myChart = (layui.carousel, layui.echarts).init(dom[0], layui.echartsTheme);
                myChart.setOption(option, true);
                window.onresize = myChart.resize;
            });


            layui.jquery.ajax({
                url: '/api/echarts/failure_rate',
                dataType: 'json',
            }).done(function (res) {


                maxValue = Math.max.apply(null, res.series);
                minValue = Math.min.apply(null, res.series);
                option = {
                    title: {text: "设备故障率分析", subtext: "单位：%"},
                    tooltip: {trigger: "axis"},
                    legend: {data: ["CT01"]},
                    calculable: !0,
                    xAxis: [{
                        type: "category",
                        data: res.xAxis
                    }],
                    yAxis: [{type: "value"}],
                    series:
                        [
                            {
                                name: "CT01", type: "line",
                                data: res.series,
                                markPoint: {
                                    data: [
                                        {
                                            name: "年最高",
                                            value: maxValue,
                                            xAxis: res.series.length,
                                            yAxis: maxValue,
                                            symbolSize: 18
                                        },
                                        {
                                            name: "年最低",
                                            value: minValue,
                                            xAxis: res.series.length,
                                            yAxis: minValue
                                        }
                                    ]
                                },
                                markLine: {data: [{type: "average", name: "平均值"}]}
                            }
                        ]
                };

                dom = layui.jquery("#LAY-index-pagethree").children("div");
                myChart = (layui.carousel, layui.echarts).init(dom[0], layui.echartsTheme);
                myChart.setOption(option, true);
                window.onresize = myChart.resize;
            });

            layui.jquery.ajax({
                url: '/api/echarts/efficiency',
                dataType: 'json',
            }).done(function (res) {
                maxValue = Math.max.apply(null, res.series);
                minValue = Math.min.apply(null, res.series);
                avgValue = Math.round(res.series.reduce((a, b) => a + b) / res.series.length);

                layui.jquery("#month-max-inspection").text(maxValue);
                layui.jquery("#month-min-inspection").text(minValue);
                layui.jquery("#month-avg-inspection").text(avgValue);
                option = {
                    title: {text: "设备效率趋势分析", subtext: "单位：人次"},
                    tooltip: {trigger: "axis"},
                    legend: {data: ["检查人次"]},
                    calculable: !0,
                    xAxis: [{
                        type: "category",
                        data: res.xAxis//["1月", "2月", "3月", "4月", "5月", "6月", "7月", "8月", "9月", "10月", "11月", "12月"]
                    }],
                    yAxis: [{type: "value"}],
                    series:
                        [
                            {
                                name: "检查人次", type: "line",
                                data: res.series,// [2.6, 5.9, 9, 26.4, 28.7, 70.7, 175.6, 182.2, 48.7, 18.8, 6, 2.3],
                                markPoint: {
                                    data: [
                                        {
                                            name: "年最高",
                                            value: maxValue,
                                            xAxis: res.series.length,
                                            yAxis: maxValue,
                                            symbolSize: 18
                                        },
                                        {
                                            name: "年最低",
                                            value: minValue,
                                            xAxis: res.series.length,
                                            yAxis: minValue
                                        }
                                    ]
                                },
                                markLine: {data: [{type: "average", name: "平均值"}]}
                            }
                        ]
                };

                dom = layui.jquery("#LAY-index-pagetwo").children("div");
                myChart = (layui.carousel, layui.echarts).init(dom[0], layui.echartsTheme);
                myChart.setOption(option, true);
                window.onresize = myChart.resize;

            });

            layui.jquery.ajax({
                url: '/api/echarts/efficiency_compare',
                dataType: 'json',
            }).done(function (res) {
                layui.jquery("[lay-filter=compare-to-last-year]").children().children().text(res.percent + '%');
                layui.element.progress('compare-to-last-year', res.percent + '%');
            });

            layui.jquery.ajax({
                url: '/api/echarts/benefit_compare',
                dataType: 'json',
            }).done(function (res) {
                layui.element.init();
                layui.element.progress('benefit-year-compare', res.yearComparePercent + '%');
                layui.element.progress('benefit-revenue-compare', res.revenuePercent + '%');
                layui.element.progress('benefit-cost-compare', res.costPercent + '%');
                layui.jquery('#benefit-revenue-rate').text(res.benefitRevenueRate);
            });

            layui.jquery.ajax({
                url: '/api/echarts/efficiency_compare',
                dataType: 'json',
            }).done(function (res) {
                layui.element.init();
                layui.element.progress('efficiency-max-compare', res.maxComparePercent + '%');
                layui.element.progress('efficiency-min-compare', res.minComparePercent + '%');
                layui.element.progress('efficiency-avg-compare', res.avgComparePercent + '%');
            });
        });
        exports('simple', this);
    }
);
