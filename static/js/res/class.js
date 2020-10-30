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
                    url: '/api/echarts/equipsbyid',
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

                layui.jquery('#analysis-last-year').on('click', function () {
                    equipment_risk_analysis_lastyear($('#equips').val() || defaultLoadEquipId);
                });

                layui.jquery('#analysis-current-year').on('click', function () {
                    equipment_risk_analysis($('#equips').val() || defaultLoadEquipId);
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
                    elem: '#item'
                    , url: '/api/dashboard/iteminfo/'
                    , where: {id: equipId}
                    , cellMinWidth: 80 //全局定义常规单元格的最小宽度，layui 2.2.1 新增
                    , cols: [[
                        {field: 'item_id', title: '设备ID'}
                        , {field: 'code', title: '设备编码'}
                        , {field: 'name', title: '设备名称'}
                        , {field: 'brand', title: '品牌'}
                        , {field: 'model', title: '型号'}
                        , {field: 'start_date', title: '启用日期'}
                        , {field: 'purchase_price', title: '采购价格'}
                    ]]
                });
                layui.table.render({
                    elem: '#workorder'
                    , url: '/api/dashboard/workorder/'
                    , where: {id: equipId}
                    , cellMinWidth: 80 //全局定义常规单元格的最小宽度，layui 2.2.1 新增
                    , cols: [[
                        {field: 'code', title: '维修工单号'}
                        , {field: 'report_time', title: '报修时间'}
                        , {field: 'complete_time', title: '完修时间'}
                        , {field: 'status', title: '状态'}
                        , {field: 'is_halt', title: '是否故障停机'}
                        , {field: 'halt_time', title: '故障停机时间'}
                        , {field: 'user_name', title: '接修人'}

                    ]]
                });
                layui.table.render({
                    elem: '#measure'
                    , url: '/api/dashboard/measure/'
                    , where: {id: equipId}
                    , cellMinWidth: 80 //全局定义常规单元格的最小宽度，layui 2.2.1 新增
                    , cols: [[
                        {field: 'qc_time', title: '质控日期'}
                        , {field: 'type', title: '质控类型'}
                        , {field: 'is_measure', title: '是否计量'}
                        , {field: 'user_name', title: '质控人员'}
                        , {field: 'result', title: '结果'}
                    ]]
                });
            });
        }

//今年设备风险分析
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
                    url: '/api/dashboard/maitenance_rate_cal',
                    dataType: 'json',
                    data: {id: equipId}
                }).done(function (res) {
                    layui.jquery('#fault-rate').text(res.fault_rate);
                    layui.element.progress('fault-rate-bar', Math.abs(res.fault_rate * 100) + '%');
                    chPBClass(res.fault_rate * 100 < 0, 'fault-rate-bar');
                    // usage_rate;
                    layui.jquery('#usage-rate').text(res.usage_rate);
                    layui.element.progress('usage-rate-bar', Math.abs(res.usage_rate * 100) + '%');
                    chPBClass(res.usage_rate * 100 < 0, 'usage-rate-bar');
                    // cost_rate;
                    layui.jquery('#cost-rate').text(res.cost_rate);
                    layui.element.progress('cost-rate-bar', Math.abs(res.cost_rate * 100) + '%');
                    chPBClass(res.cost_rate * 100 < 0, 'cost-rate-bar');
                });
            });
        }

//去年设备风险分析
        function equipment_risk_analysis_lastyear(equipId) {
            layui.use(["carousel", "echarts"], function () {
                layui.jquery.ajax({
                    url: '/api/dashboard/maintenance_data_lastyear',
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
                    url: '/api/dashboard/maitenance_rate_cal_lastyear',
                    dataType: 'json',
                    data: {id: equipId}
                }).done(function (res) {
                    layui.jquery('#fault-rate').text(res.fault_rate);
                    layui.element.progress('fault-rate-bar', Math.abs(res.fault_rate * 100) + '%');
                    chPBClass(res.fault_rate * 100 < 0, 'fault-rate-bar');
                    // usage_rate;
                    layui.jquery('#usage-rate').text(res.usage_rate);
                    layui.element.progress('usage-rate-bar', Math.abs(res.usage_rate * 100) + '%');
                    chPBClass(res.usage_rate * 100 < 0, 'usage-rate-bar');
                    // cost_rate;
                    layui.jquery('#cost-rate').text(res.cost_rate);
                    layui.element.progress('cost-rate-bar', Math.abs(res.cost_rate * 100) + '%');
                    chPBClass(res.cost_rate * 100 < 0, 'cost-rate-bar');
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

