<?php /*a:1:{s:73:"/Applications/MAMP/htdocs/operator/application/index/view/index/home.html";i:1598765238;}*/ ?>
<!DOCTYPE html>
<html style="height: 100%">
<head>
    <meta charset="utf-8">
    <title>主页一</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    
    <link rel="stylesheet" type="text/css" href="/static/layui/css/layui.css" />
    <link rel="stylesheet" type="text/css" href="/static/css/public/public.css" />
    <link rel="stylesheet" type="text/css" href="/static/css/iconfont/iconfont.css" />
</head>
<style>
    .layui-top-box {padding:20px;color:#fff}
    .panel {margin-bottom:17px;background-color:#eee;border:1px solid transparent;border-radius: 5px;-webkit-box-shadow:0 1px 1px rgba(0,0,0,.05);box-shadow:0 1px 1px rgba(0,0,0,.05)}
    .panel-body {height: 75px;}
    .panel-icon {float: left; height: 75px; line-height: 75px; border-bottom-left-radius: 5px; border-top-left-radius: 5px; text-align: center}
    .panel-icon i {font-size: 45px; }
    .panel-info {float: left; padding: 10px; text-align: right;}
    .panel-info span {display: block;}
    .panel-info .counts {font-size: 30px; color: #333; line-height: 40px; text-shadow: 2px 1px 1px #999;}
    .panel-info .counts-name {font-size: 15px; color: #999; line-height: 20px;}
</style>
<body>

<div class="layuimini-container">
    <div class="layuimini-main layui-top-box">
        <div class="layui-row layui-col-space15">
            <div class="layui-col-md3">
                <div class="col-xs-6 col-md-3">
                    <div class="panel">
                        <div class="panel-body">
                            <div class="panel-icon layui-col-xs3 layui-col-md3 layui-bg-cyan">
                                <i class="iconfont">&#xe936;</i>
                            </div>
                            <div class="panel-info layui-col-xs9 layui-col-md9">
                                <span class="counts">5</span>
                                <span class="counts-name">品类数量</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-col-md3">
                <div class="col-xs-6 col-md-3">
                    <div class="panel">
                        <div class="panel-body">
                            <div class="panel-icon layui-col-xs3 layui-col-md3 layui-bg-red">
                                <i class="iconfont">&#xea62;</i>
                            </div>
                            <div class="panel-info layui-col-xs9 layui-col-md9">
                                <span class="counts">10</span>
                                <span class="counts-name">设备数量</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-col-md3">
                <div class="col-xs-6 col-md-3">
                    <div class="panel">
                        <div class="panel-body">
                            <div class="panel-icon layui-col-xs3 layui-col-md3 layui-bg-orange">
                                <i class="iconfont">&#xe7f0;</i>
                            </div>
                            <div class="panel-info layui-col-xs9 layui-col-md9">
                                <span class="counts">100</span>
                                <span class="counts-name">当年检查人次</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-col-md3">
                <div class="col-xs-6 col-md-3">
                    <div class="panel">
                        <div class="panel-body">
                            <div class="panel-icon layui-col-xs3 layui-col-md3 layui-bg-green">
                                <i class="iconfont">&#xe7fe;</i>
                            </div>
                            <div class="panel-info layui-col-xs9 layui-col-md9">
                                <span class="counts">100</span>
                                <span class="counts-name">当年总收入</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <fieldset class="layui-elem-field layui-field-title" style="margin-top: 50px;">
        <legend>月收入趋势</legend>
    </fieldset>
    <div id="container" style="height: 350px; margin: 0"></div>
    <fieldset class="layui-elem-field layui-field-title" style="margin-top: 50px;">
        <legend>设备分类概览</legend>
    </fieldset>

    <div class="layui-tab layui-tab-brief" lay-filter="docDemoTabBrief" style="height:400px;">
        <ul class="layui-tab-title">
            <li class="layui-this">CT概览</li>
            <li>MRI概览</li>
            <li>X光机概览</li>
            <li>PET-CT概览</li>
            <li>DSA概览</li>
        </ul>
        <div class="layui-tab-content" style="height: 100px;">
            <div class="layui-tab-item layui-show"> <div class="layui-top-box">
                <div class="layui-row layui-col-space15">
                    <table class="layui-table">
                        <thead>
                        <tr>
                            <th colspan="2" style="font-weight: bold;">CT数据概览</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th style="width:250px">设备全称</th>
                            <td>X射线计算机断层摄影设备</td>
                        </tr>
                        <tr>
                            <th>设备数量</th>
                            <td>7</td>
                        </tr>

                        <tr>
                            <th>当年检查人次</th>
                            <td>1000人次</td>
                        </tr>
                        <tr>
                            <th>当年检查总收入</th>
                            <td>¥234234</td>
                        </tr>
                        <tr>
                            <th>检查阳性率</th>
                            <td>87%</td>
                        </tr>
                        <tr>
                            <th>本月故障率（故障台数/总台数）</th>
                            <td>10%</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

            </div></div>
            <div class="layui-tab-item"> <div class="layui-top-box">
                <div class="layui-row layui-col-space15">
                    <table class="layui-table">
                        <thead>
                        <tr>
                            <th colspan="2" style="font-weight: bold;">MRI数据概览</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th style="width:250px">设备全称</th>
                            <td>核磁共振成像系统</td>
                        </tr>
                        <tr>
                            <th>设备数量</th>
                            <td>2</td>
                        </tr>

                        <tr>
                            <th>当年检查人次</th>
                            <td>1000人次</td>
                        </tr>
                        <tr>
                            <th>当年检查总收入</th>
                            <td>¥234234</td>
                        </tr>
                        <tr>
                            <th>检查阳性率</th>
                            <td>87%</td>
                        </tr>
                        <tr>
                            <th>本月故障率（故障台数/总台数）</th>
                            <td>10%</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

            </div></div>
            <div class="layui-tab-item"><div class="layui-top-box">
                <div class="layui-row layui-col-space15">
                    <table class="layui-table">
                        <thead>
                        <tr>
                            <th colspan="2" style="font-weight: bold;">MRI数据概览</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th style="width:250px">设备全称</th>
                            <td>核磁共振成像系统</td>
                        </tr>
                        <tr>
                            <th>设备数量</th>
                            <td>2</td>
                        </tr>

                        <tr>
                            <th>当年检查人次</th>
                            <td>1000人次</td>
                        </tr>
                        <tr>
                            <th>当年检查总收入</th>
                            <td>¥234234</td>
                        </tr>
                        <tr>
                            <th>检查阳性率</th>
                            <td>87%</td>
                        </tr>
                        <tr>
                            <th>本月故障率（故障台数/总台数）</th>
                            <td>10%</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

            </div></div>
            <div class="layui-tab-item"><div class="layui-top-box">
                <div class="layui-row layui-col-space15">
                    <table class="layui-table">
                        <thead>
                        <tr>
                            <th colspan="2" style="font-weight: bold;">MRI数据概览</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th style="width:250px">设备全称</th>
                            <td>核磁共振成像系统</td>
                        </tr>
                        <tr>
                            <th>设备数量</th>
                            <td>2</td>
                        </tr>

                        <tr>
                            <th>当年检查人次</th>
                            <td>1000人次</td>
                        </tr>
                        <tr>
                            <th>当年检查总收入</th>
                            <td>¥234234</td>
                        </tr>
                        <tr>
                            <th>检查阳性率</th>
                            <td>87%</td>
                        </tr>
                        <tr>
                            <th>本月故障率（故障台数/总台数）</th>
                            <td>10%</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

            </div></div>
            <div class="layui-tab-item"><div class="layui-top-box">
                <div class="layui-row layui-col-space15">
                    <table class="layui-table">
                        <thead>
                        <tr>
                            <th colspan="2" style="font-weight: bold;">MRI数据概览</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th style="width:250px">设备全称</th>
                            <td>核磁共振成像系统</td>
                        </tr>
                        <tr>
                            <th>设备数量</th>
                            <td>2</td>
                        </tr>

                        <tr>
                            <th>当年检查人次</th>
                            <td>1000人次</td>
                        </tr>
                        <tr>
                            <th>当年检查总收入</th>
                            <td>¥234234</td>
                        </tr>
                        <tr>
                            <th>检查阳性率</th>
                            <td>87%</td>
                        </tr>
                        <tr>
                            <th>本月故障率（故障台数/总台数）</th>
                            <td>10%</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

            </div></div>
        </div>
    </div>


</div>

<script type="text/javascript" src="/static/js/jquery-3.4.1.min.js"></script>
<script type="text/javascript" src="/static/layui/layui.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>

<script type="text/javascript">
    var dom = document.getElementById("container");
    var myChart = echarts.init(dom);

    option = null;
    option = {
        xAxis: {
            type: 'category',
            data: ['2020-01', '2020-02', '2020-03', '2020-04', '2020-05', '2020-06', '2020-07', '2020-08']
        },
        yAxis: {
            type: 'value'
        },
        series: [{
            data: [563,820, 932, 901, 934, 1290, 1330, 1320],
            type: 'line'
        }]
    };
    ;
    if (option && typeof option === "object") {
        myChart.setOption(option, true);
    }
</script>
<script>
    layui.use('element', function(){
        var $ = layui.jquery
            ,element = layui.element; //Tab的切换功能，切换事件监听等，需要依赖element模块

        //触发事件
        var active = {
            tabAdd: function(){
                //新增一个Tab项
                element.tabAdd('demo', {
                    title: '新选项'+ (Math.random()*1000|0) //用于演示
                    ,content: '内容'+ (Math.random()*1000|0)
                    ,id: new Date().getTime() //实际使用一般是规定好的id，这里以时间戳模拟下
                })
            }
            ,tabDelete: function(othis){
                //删除指定Tab项
                element.tabDelete('demo', '44'); //删除：“商品管理”


                othis.addClass('layui-btn-disabled');
            }
            ,tabChange: function(){
                //切换到指定Tab项
                element.tabChange('demo', '22'); //切换到：用户管理
            }
        };

        $('.site-demo-active').on('click', function(){
            var othis = $(this), type = othis.data('type');
            active[type] ? active[type].call(this, othis) : '';
        });

        //Hash地址的定位
        var layid = location.hash.replace(/^#test=/, '');
        element.tabChange('test', layid);

        element.on('tab(test)', function(elem){
            location.hash = 'test='+ $(this).attr('lay-id');
        });

    });
</script>

</body>
</html>