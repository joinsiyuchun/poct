/** layuiAdmin.std-v1.0.0 LPPL License By http://www.layui.com/admin/ */


layui.define
(
    function (exports) {

        var defaultLoadEquipId = '47';

//页面初始化
        function initPage() {
            layui.use(["element", "table", "admin", "carousel", "echarts"], function () {
                var element = layui.element
                element.init();

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
                    url: '/api/echarts/equips',
                    dataType: 'json'
                }).done(function (res) {
                    $('#equips').empty();
                    $('#equips').append($(' <option value="">请选择设备</option>'));
                    for (var prop in res) {
                        var group = $('<optgroup label="' + prop + '"></optgroup>');
                        var val = res[prop];

                        val.map(function (equip) {
                            var option = $('<option value="' + equip.id + '">' + equip.code + '</option>');
                            group.append(option);
                        });
                        $('#equips').append(group);
                    }

                    layui.use('form', function () {
                        layui.form.render();
                    });
                });

                layui.jquery('#equip-query').on('click', function () {
                    reload($('#equips').val() || defaultLoadEquipId);
                });


                layui.jquery('#inspection-current-year').on('click', function () {
                    efficiency_current_year($('#equips').val() || defaultLoadEquipId);
                });


                layui.jquery('#inspection-last-year').on('click', function () {
                    efficiency_last_year($('#equips').val() || defaultLoadEquipId);
                });


                layui.jquery('#benefit-last-year').on('click', function () {
                    benefit_last_year($('#equips').val() || defaultLoadEquipId);
                });

                layui.jquery('#benefit-current-year').on('click', function () {
                    benefit_current_year($('#equips').val() || defaultLoadEquipId);
                });

                layui.jquery('#failure_rate_last').on('click', function () {
                    failure_rate_last_year($('#equips').val() || defaultLoadEquipId);
                });

                layui.jquery('#failure_rate_current').on('click', function () {
                    failure_rate_current_year($('#equips').val() || defaultLoadEquipId);
                });

            });
        }

        function chPBClass(bRed, id) {
            if (bRed) {
                layui.jquery('#' + id).children().removeClass('layui-bg-green').addClass('layui-bg-red');
            } else {
                layui.jquery('#' + id).children().removeClass('layui-bg-red').addClass('layui-bg-green');
            }
        }

//效率去年
        function efficiency_last_year(equipId) {
            layui.use(["carousel", "echarts"], function () {
                layui.jquery.ajax({
                    url: '/api/echarts/efficiency_last_year',
                    dataType: 'json',
                    data: {id: equipId}
                }).done(function (res) {
                    maxValue = Math.max.apply(null, res.series);
                    minValue = Math.min.apply(null, res.series);
                    avgValue = 0;//Math.round(res.series.reduce((a, b) => a + b) / res.series.length);

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
                                        data: [{type: "max", name: "最大值", symbolSize: 18}, {
                                            type: "min",
                                            name: "最小值"
                                        }]
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
                    url: '/api/echarts/efficiency_avg_last_year',
                    dataType: 'json',
                    data: {id: equipId}
                }).done(function (res) {

                    layui.jquery("#month-avg-inspection").text(res.inspection_times_per);
                    layui.element.progress('month-avg-inspection-percent-bar', Math.abs(res.inspection_times_per_mom * 100) + '%');
                    layui.jquery('#month-avg-inspection-percent').text(res.inspection_times_per_mom * 100 + '%').attr('style', res.inspection_times_per_mom * 100 > 0 ? 'color:green' : 'color:red');
                    chPBClass(res.inspection_times_per_mom * 100 < 0, 'month-avg-inspection-percent-bar');

                });

                layui.jquery.ajax({
                    url: '/api/echarts/efficiency_max_last_year',
                    dataType: 'json',
                    data: {id: equipId}
                }).done(function (res) {
                    layui.jquery("#month-max-inspection").text(res.inspection_times_max);
                    layui.element.progress('month-max-inspection-percent-bar', Math.abs(res.inspection_times_max_mom * 100) + '%');
                    layui.jquery('#month-max-inspection-percent').text(res.inspection_times_max_mom * 100 + '%').attr('style', res.inspection_times_max_mom * 100 > 0 ? 'color:green' : 'color:red');
                    chPBClass(res.inspection_times_max_mom * 100 < 0, 'month-max-inspection-percent-bar');

                });

                layui.jquery.ajax({
                    url: '/api/echarts/efficiency_min_last_year',
                    dataType: 'json',
                    data: {id: equipId}
                }).done(function (res) {

                    layui.jquery("#month-min-inspection").text(res.inspection_times_min);
                    layui.element.progress('month-min-inspection-percent-bar', Math.abs(res.inspection_times_min_mom * 100) + '%');
                    layui.jquery('#month-min-inspection-percent').text(res.inspection_times_min_mom * 100 + '%').attr('style', res.inspection_times_min_mom * 100 > 0 ? 'color:green' : 'color:red');
                    chPBClass(res.inspection_times_min_mom * 100 < 0, 'month-min-inspection-percent-bar');

                });
            });
        }

//效率今年
        function efficiency_current_year(equipId) {
            layui.use(["carousel", "echarts"], function () {
                layui.jquery.ajax({
                    url: '/api/echarts/efficiency_current_year',
                    dataType: 'json',
                    data: {id: equipId}
                }).done(function (res) {

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
                                        data: [{type: "max", name: "最大值", symbolSize: 18}, {
                                            type: "min",
                                            name: "最小值"
                                        }]
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
                    url: '/api/echarts/efficiency_avg_current_year',
                    dataType: 'json',
                    data: {id: equipId}
                }).done(function (res) {

                    layui.jquery("#month-avg-inspection").text(res.inspection_times_per);
                    layui.element.progress('month-avg-inspection-percent-bar', Math.abs(res.inspection_times_per_mom * 100) + '%');
                    layui.jquery('#month-avg-inspection-percent').text(res.inspection_times_per_mom * 100 + '%').attr('style', res.inspection_times_per_mom * 100 > 0 ? 'color:green' : 'color:red');
                    chPBClass(res.inspection_times_per_mom * 100 < 0, 'month-avg-inspection-percent-bar');
                });

                layui.jquery.ajax({
                    url: '/api/echarts/efficiency_max_current_year',
                    dataType: 'json',
                    data: {id: equipId}
                }).done(function (res) {
                    layui.jquery("#month-max-inspection").text(res.inspection_times_max);
                    layui.element.progress('month-max-inspection-percent-bar', Math.abs(res.inspection_times_max_mom * 100) + '%');
                    layui.jquery('#month-max-inspection-percent').text(res.inspection_times_max_mom * 100 + '%').attr('style', res.inspection_times_max_mom * 100 > 0 ? 'color:green' : 'color:red');
                    chPBClass(res.inspection_times_max_mom * 100 < 0, 'month-max-inspection-percent-bar');
                });

                layui.jquery.ajax({
                    url: '/api/echarts/efficiency_min_current_year',
                    dataType: 'json',
                    data: {id: equipId}
                }).done(function (res) {

                    layui.jquery("#month-min-inspection").text(res.inspection_times_min);
                    layui.element.progress('month-min-inspection-percent-bar', Math.abs(res.inspection_times_min_mom * 100) + '%');
                    layui.jquery('#month-min-inspection-percent').text(res.inspection_times_min_mom * 100 + '%').attr('style', res.inspection_times_min_mom * 100 > 0 ? 'color:green' : 'color:red');
                    chPBClass(res.inspection_times_min_mom * 100 < 0, 'month-min-inspection-percent-bar');

                });
            });
        }

//页头绑定
        function global_bind(equipId) {
            layui.jquery.ajax({
                url: '/api/echarts/global_month',
                dataType: 'json',
                data: {'id': equipId}
            }).done(function (res) {
                layui.jquery('#month-total-cost').text(res.cost);
                layui.jquery('#month-total-inspection').text(res.inspection);
                layui.jquery('#month-total-revenue').text(res.income);
            });

            layui.jquery.ajax({
                url: '/api/echarts/global_year',
                dataType: 'json',
                data: {id: equipId}
            }).done(function (res) {

                layui.jquery('#year-total-revenue').text(res.income);
                layui.jquery('#year-total-inspection').text(res.inspection);
                layui.jquery('#year-total-cost').text(res.cost);
            });

            layui.jquery.ajax({
                url: '/api/echarts/return_rate',
                dataType: 'json',
                data: {id: equipId}
            }).done(function (res) {
                layui.jquery('#year-return-rate').text(res.current);
                layui.jquery('#last-year-return-rate').text(res.last);
            });
        }

//表格绑定
        function table_bind(equipId) {
            layui.use(["table"], function () {
                layui.table.render({
                    elem: '#varcost'
                    , url: '/api/echarts/varcost/'
                    , where: {id: equipId}
                    , cellMinWidth: 80 //全局定义常规单元格的最小宽度，layui 2.2.1 新增
                    , cols: [[

                        {field: 'item_id', title: '设备ID'}
                        , {field: 'cost_item', title: '费用类型'}
                        , {field: 'start_date', title: '计费开始时间'}
                        , {field: 'end_date', title: '计费开始时间'}
                        , {field: 'total_cost', title: '金额', sort: true}
                    ]]
                });
                layui.table.render({
                    elem: '#fixcost'
                    , url: '/api/echarts/fixcost/'
                    , where: {id: equipId}
                    , cellMinWidth: 80 //全局定义常规单元格的最小宽度，layui 2.2.1 新增
                    , cols: [[
                        {field: 'item_id', title: '设备ID'}
                        , {field: 'cost_item', title: '费用类型'}
                        , {field: 'start_date', title: '计费开始时间'}
                        , {field: 'end_date', title: '计费开始时间'}
                        , {field: 'total_cost', title: '金额', sort: true}
                    ]]
                });
                //item_id, department, COUNT(DISTINCT request_id) as inspection_times, sum(profit) as income
                layui.table.render({
                    elem: '#dept'
                    , url: '/api/echarts/dept/'
                    , where: {id: equipId}
                    , cellMinWidth: 80 //全局定义常规单元格的最小宽度，layui 2.2.1 新增
                    , cols: [[
                        {field: 'item_id', title: '设备ID'}
                        , {field: 'department', title: '科室名称'}
                        , {field: 'inspection_times', title: '本月检查人次', sort: true}
                        , {field: 'income', title: '本月收入', sort: true}
                        , {field: 'cost', title: '本月成本', sort: true}
                    ]]
                });
                layui.table.render({
                    elem: '#source'
                    , url: '/api/echarts/source/'
                    , where: {id: equipId}
                    , cellMinWidth: 80 //全局定义常规单元格的最小宽度，layui 2.2.1 新增
                    , cols: [[
                        {field: 'item_id', title: '设备ID'}
                        , {field: 'patient_source', title: '检查来源'}
                        , {field: 'inspection_times', title: '本月检查人次'}
                        , {field: 'income', title: '本月收入'}
                        , {field: 'cost', title: '本月成本', sort: true}
                    ]]
                });
            });
        }

//效益去年
        function benefit_last_year(equipId) {
            layui.use(["carousel", "echarts"], function () {
                layui.jquery.ajax({
                    url: '/api/echarts/benefit_last_year',
                    dataType: 'json',
                    data: {id: equipId}
                }).done(function (res) {

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
                                    data: res.cost,
                                    markPoint: {
                                        data: [{type: "max", name: "最大值", symbolSize: 8}, {
                                            type: "min",
                                            name: "最小值",
                                            symbolSize: 8
                                        }]
                                    },
                                    markLine: {data: [{type: "average", name: "平均值"}]}
                                },
                                {
                                    name: "收入", type: "bar",
                                    data: res.income,//[2.6, 5.9, 9, 26.4, 28.7, 70.7, 175.6, 182.2, 48.7, 18.8, 6, 2.3],
                                    markPoint: {data: [{type: "max", name: "最大值"}, {type: "min", name: "最小值"}]},
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
                    url: '/api/echarts/return_rate_last_year',
                    dataType: 'json',
                    data: {id: equipId}
                }).done(function (res) {

                    layui.jquery('#benefit-return-rate').text(res.return_rate);
                    layui.element.progress('benefit-return-rate-bar', Math.abs(res.return_rate_mom * 100) + '%');
                    layui.jquery('#benefit-return-rate-percent').text(res.return_rate_mom * 100 + '%').attr('style', res.return_rate_mom * 100 > 0 ? 'color:green' : 'color:red');
                    chPBClass(res.return_rate_mom * 100 < 0, 'benefit-return-rate-bar');
                    // layui.jquery('#month-avg-cost').text(avgCostValue);

                });

                layui.jquery.ajax({
                    url: '/api/echarts/income_per_mon_last_year',
                    dataType: 'json',
                    data: {id: equipId}
                }).done(function (res) {

                    layui.jquery('#benefit-avg-income').text(res.income_per);
                    layui.element.progress('benefit-avg-income-bar', Math.abs(res.income_per_mom * 100) + '%');
                    layui.jquery('#benefit-avg-income-percent').text(res.income_per_mom * 100 + '%').attr('style', res.income_per_mom * 100 > 0 ? 'color:green' : 'color:red');
                    chPBClass(res.income_per_mom * 100 < 0, 'benefit-avg-income-bar');
                    // layui.jquery('#month-avg-cost').text(avgCostValue);

                });

                layui.jquery.ajax({
                    url: '/api/echarts/cost_per_mon_last_year',
                    dataType: 'json',
                    data: {id: equipId}
                }).done(function (res) {

                    layui.jquery('#benefit-avg-cost').text(res.cost_per);
                    layui.element.progress('benefit-avg-cost-bar', Math.abs(res.cost_per_mom * 100) + '%');
                    layui.jquery('#benefit-avg-cost-percent').text(res.cost_per_mom * 100 + '%').attr('style', res.cost_per_mom * 100 > 0 ? 'color:green' : 'color:red');
                    chPBClass(res.cost_per_mom * 100 < 0, 'benefit-avg-cost-bar');
                    // layui.jquery('#month-avg-cost').text(avgCostValue);

                });
            });
        }

//效益今年
        function benefit_current_year(equipId) {
            layui.use(["carousel", "echarts"], function () {
                layui.jquery.ajax({
                    url: '/api/echarts/benefit_current_year',
                    dataType: 'json',
                    data: {id: equipId}
                }).done(function (res) {

                    // layui.jquery('#month-avg-revenue').text(avgRevenueValue);
                    // layui.jquery('#month-avg-cost').text(avgCostValue);

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
                                    data: res.cost,
                                    markPoint: {
                                        data: [{type: "max", name: "最大值", symbolSize: 8}, {
                                            type: "min",
                                            name: "最小值",
                                            symbolSize: 8
                                        }]
                                    },
                                    markLine: {data: [{type: "average", name: "平均值"}]}
                                },
                                {
                                    name: "收入", type: "bar",
                                    data: res.income,//[2.6, 5.9, 9, 26.4, 28.7, 70.7, 175.6, 182.2, 48.7, 18.8, 6, 2.3],
                                    markPoint: {data: [{type: "max", name: "最大值"}, {type: "min", name: "最小值"}]},
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
                    url: '/api/echarts/return_rate_current_year',
                    dataType: 'json',
                    data: {id: equipId}
                }).done(function (res) {

                    layui.jquery('#benefit-return-rate').text(res.return_rate);
                    layui.element.progress('benefit-return-rate-bar', Math.abs(res.return_rate_mom * 100) + '%');
                    layui.jquery('#benefit-return-rate-percent').text(res.return_rate_mom * 100 + '%').attr('style', res.return_rate_mom * 100 > 0 ? 'color:green' : 'color:red');

                    chPBClass(res.return_rate_mom * 100 < 0, 'benefit-return-rate-bar');

                    // layui.jquery('#month-avg-cost').text(avgCostValue);

                });

                layui.jquery.ajax({
                    url: '/api/echarts/income_per_mon_current_year',
                    dataType: 'json',
                    data: {id: equipId}
                }).done(function (res) {

                    layui.jquery('#benefit-avg-income').text(res.income_per);
                    layui.element.progress('benefit-avg-income-bar', Math.abs(res.income_per_mom * 100) + '%');
                    layui.jquery('#benefit-avg-income-percent').text(res.income_per_mom * 100 + '%').attr('style', res.income_per_mom * 100 > 0 ? 'color:green' : 'color:red');
                    chPBClass(res.income_per_mom * 100 < 0, 'benefit-avg-income-bar');
                    // layui.jquery('#month-avg-cost').text(avgCostValue);

                });

                layui.jquery.ajax({
                    url: '/api/echarts/cost_per_mon_current_year',
                    dataType: 'json',
                    data: {id: equipId}
                }).done(function (res) {

                    layui.jquery('#benefit-avg-cost').text(res.cost_per);
                    layui.element.progress('benefit-avg-cost-bar', Math.abs(res.cost_per_mom * 100) + '%');
                    layui.jquery('#benefit-avg-cost-percent').text(res.cost_per_mom * 100 + '%').attr('style', res.cost_per_mom * 100 > 0 ? 'color:green' : 'color:red');

                    chPBClass(res.cost_per_mom * 100 < 0, 'benefit-avg-cost-bar');

                    // layui.jquery('#month-avg-cost').text(avgCostValue);

                });
            });
        }

        function failure_rate_current_year(equipId) {
            layui.use(["carousel", "echarts"], function () {
                layui.jquery.ajax({
                    url: '/api/echarts/failure_rate_current_year',
                    dataType: 'json',
                    data: {id: equipId}
                }).done(function (res) {
                    console.log(res)

                    var option = {
                        title: {text: "设备故障率分析", subtext: "单位：%"},
                        tooltip: {trigger: "axis"},
                        legend: {data: ["设备故障分析"]},
                        calculable: !0,
                        xAxis: [{
                            type: "category",
                            data: res.xAxis
                        }],
                        yAxis: [{type: "value"}],
                        series: [
                            {
                                data: res.series,
                                name: "设备故障次数", type: "line",
                                markPoint: {
                                    data: [{type: "max", name: "最大值", symbolSize: 18}, {
                                        type: "min",
                                        name: "最小值"
                                    }]
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
            });
        }

        function failure_rate_last_year(equipId) {
            layui.use(["carousel", "echarts"], function () {
                layui.jquery.ajax({
                    url: '/api/echarts/failure_rate_last_year',
                    dataType: 'json',
                    data: {id: equipId}
                }).done(function (res) {
                    console.log(res)

                    var option = {
                        title: {text: "设备故障率分析", subtext: "单位：%"},
                        tooltip: {trigger: "axis"},
                        legend: {data: ["设备故障分析"]},
                        calculable: !0,
                        xAxis: [{
                            type: "category",
                            data: res.xAxis
                        }],
                        yAxis: [{type: "value"}],
                        series: [
                            {
                                data: res.series,
                                name: "设备故障次数", type: "line",
                                markPoint: {
                                    data: [{type: "max", name: "最大值", symbolSize: 18}, {
                                        type: "min",
                                        name: "最小值"
                                    }]
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
            });
        }

        function efficiency_compare(equipId) {
            layui.use(["carousel", "echarts"], function () {

                layui.jquery.ajax({
                    url: '/api/echarts/efficiency_compare',
                    dataType: 'json',
                    data: {id: equipId}
                }).done(function (res) {
                    layui.jquery("[lay-filter=compare-to-last-year]").children().children().text(res.percent + '%');
                    layui.element.progress('compare-to-last-year', res.percent + '%');
                });
                layui.jquery.ajax({
                    url: '/api/echarts/benefit_efficiency_compare',
                    dataType: 'json',
                    data: {id: equipId}
                }).done(function (res) {

                    ids = ['benefit-year-compare', 'benefit-revenue-compare', 'benefit-cost-compare', 'efficiency-max-compare', 'efficiency-min-compare', 'efficiency-avg-compare'];
                    ids.forEach(function (id) {
                        key = id.replace(/\-[a-z]/g, function (txtjq) {
                            return txtjq.toUpperCase().replace("-", "");
                        });
                        layui.element.progress(id, Math.abs(res[key]) + '%');
                        layui.jquery('#' + id + '-value').text(res[key] + '%').attr('style', res[key] > 0 ? 'color:green' : 'color:red');
                        if (res[key] < 0) {
                            layui.jquery('#' + id).children().addClass('layui-bg-red');
                        }
                    });

                    layui.jquery('#benefit-revenue-rate').text(res.benefitRevenueRate);
                });
            });
        }

        function reload(equipId) {
            table_bind(equipId);
            global_bind(equipId);
            failure_rate_current_year(equipId);
            benefit_current_year(equipId);
            efficiency_current_year(equipId);
        }

        initPage();
        reload(defaultLoadEquipId);
        exports('simple', this);
    }
);

