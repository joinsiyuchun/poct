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
                            if (defaultLoadEquipId == 0) {
                                defaultLoadEquipId = equip.id;
                            }
                            var option = $('<option value="' + equip.id + '">' + equip.code + '</option>');
                            group.append(option);
                        });
                        $('#equips').append(group);

                    }

                    layui.use('form', function () {
                        $('#equips').val(defaultLoadEquipId);
                        reload(defaultLoadEquipId);
                        layui.form.render();
                    });
                });

                layui.jquery('#equip-query').on('click', function () {
                    reload($('#equips').val() || defaultLoadEquipId);
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


//页头绑定
        function global_bind(equipId) {
            layui.jquery.ajax({
                url: '/api/dashboard/cur_repair',
                dataType: 'json',
                data: {'id': equipId}
            }).done(function (res) {
                layui.jquery('#total_report_num').text(res.total_num);
                layui.jquery('#total_repair_time').text(res.total_time);

            });

            layui.jquery.ajax({
                url: '/api/dashboard/month_repair',
                dataType: 'json',
                data: {id: equipId}
            }).done(function (res) {
                layui.jquery('#month_report_num').text(res.total_num);
                layui.jquery('#month_repair_time').text(res.total_time);

            });

            layui.jquery.ajax({
                url: '/api/dashboard/year_contract_cost',
                dataType: 'json',
                data: {id: equipId}
            }).done(function (res) {
                layui.jquery('#year_contract_cost').text(res.total_cost);
            });

            layui.jquery.ajax({
                url: '/api/dashboard/year_single_cost',
                dataType: 'json',
                data: {id: equipId}
            }).done(function (res) {
                layui.jquery('#year_single_cost').text(res.total_cost);
            });

            layui.jquery.ajax({
                url: '/api/dashboard/usage_period',
                dataType: 'json',
                data: {id: equipId}
            }).done(function (res) {
                layui.jquery('#month_gap').text(res.month_gap);
            });

            layui.jquery.ajax({
                url: '/api/dashboard/dep_period',
                dataType: 'json',
                data: {id: equipId}
            }).done(function (res) {
                layui.jquery('#dep_period').text(res.depreciation_period);
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

                        {field: 'item_id', title: '计量日期'}
                        , {field: 'cost_item', title: '检测类型'}
                        , {field: 'start_date', title: '是否计量'}
                        , {field: 'end_date', title: '送检人员'}
                        , {field: 'total_cost', title: '检查结果', sort: true}
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
                        , {field: 'department', title: '设备编码'}
                        , {field: 'inspection_times', title: '设备名称', sort: true}
                        , {field: 'income', title: '启用日期', sort: true}
                        , {field: 'cost', title: '购买成本', sort: true}
                    ]]
                });
                layui.table.render({
                    elem: '#source'
                    , url: '/api/echarts/source/'
                    , where: {id: equipId}
                    , cellMinWidth: 80 //全局定义常规单元格的最小宽度，layui 2.2.1 新增
                    , cols: [[
                        {field: 'item_id', title: '报修日期'}
                        , {field: 'patient_source', title: '接修日期'}
                        , {field: 'inspection_times', title: '完修日期'}
                        , {field: 'income', title: '结算金额'}
                        , {field: 'cost', title: '是否保内', sort: true}
                    ]]
                });
            });
        }

//设备风险分析
        function equipment_risk_analysis(equipId) {
            layui.use(["carousel", "echarts"], function () {
                layui.jquery.ajax({
                    url: '/api/dashboard/maintenance_data',
                    dataType: 'json',
                    data: {id: equipId}
                }).done(function (res) {

                    option = {
                        tooltip: {trigger: "axis"},
                        legend:{x:"left",data:["同类台均",res[1].name]},
                        polar:[
                            {indicator:
                                    [
                                        {text:"故障次数"},
                                        {text:"PM完成次数"},
                                        {text:"计量完成次数"},
                                        {text:"强检完成次数"},
                                        {text:"使用年限"}
                                    ],
                                radius:130
                            }
                        ],
                        series:
                            [{
                                type: "radar",
                                center: ["50%", "50%"],
                                data: res
                            }]
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

        function reload(equipId) {
            table_bind(equipId);
            global_bind(equipId);
            equipment_risk_analysis(equipId);
        }

        initPage();
        exports('class', this);
    }
);

