<?php /*a:2:{s:67:"/var/www/html/operator/application/index/view/qualitylog/index.html";i:1599656086;s:64:"/var/www/html/operator/application/index/view/public/layout.html";i:1599656086;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="keywords" content="">
    <meta name="description" content="">
    <title><?php echo htmlentities((isset($title) && ($title !== '')?$title:"一键修设备维修管理系统")); ?></title>
    
    <link rel="stylesheet" type="text/css" href="/static/css/public/public.css" />
    <link rel="stylesheet" type="text/css" href="/static/layui/css/layui.css" />
    <link rel="stylesheet" type="text/css" href="/static/css/iconfont/iconfont.css" />
    <link rel="stylesheet" type="text/css" href="/static/css/admin/admin.css" />
    
    
</head>
<body class="layui-layout-body">
    <div class="layui-card">
        <div class="layui-card-header">
            <i class="iconfont">&#xe755;</i> <?php echo htmlentities($title); ?></i>
        </div>
        <div class="layui-card-body">
            

<table class="layui-hide" id="dataTable" lay-filter="dataTable"></table>

<script type="text/html" id="toolbar">
    <div class="dataToolbar">
        <div class="layui-inline">
            <input class="layui-input" name="keywords" id="keywords" value="<?php echo input('keywords'); ?>" autocomplete="on" placeholder="请输入检查人员姓名">
        </div>
        <button class="layui-btn search-btn" data-type="reload"><i class="iconfont">&#xe679;</i> 查询</button>
    </div>
</script>


<script type="text/html" id="barTool">
    <?php if((buttonCheck('index/delete'))): ?>
    <a href='javascript:;' lay-event="del" class="layui-btn layui-btn-danger layui-btn-xs"><i class="iconfont">&#xe6b4;</i> 删除</a>
    <?php endif; ?>
</script>


<script type="text/html" id="status">
    {{# if(d.qc_status == 1){ }}
    <button class="layui-btn layui-btn-xs layui-btn-warm">部分通过</button>
    {{# } else if(d.qc_status == 0){ }}
    <button class="layui-btn layui-btn-xs layui-btn-danger">未通过</button>
    {{# } else if(d.qc_status == 2){ }}
    <button class="layui-btn layui-btn-xs layui-btn-success">完全通过</button>
    {{# } }}
</script>


<script type="text/html" id="type">
    {{# if(d.type == 1){ }}
    <button class="layui-btn layui-btn-xs layui-btn-warm">每日巡检</button>
    {{# } else if(d.type == 3){ }}
    <button class="layui-btn layui-btn-xs layui-btn-danger">预防性维护</button>
    {{# } else if(d.type == 2){ }}
    <button class="layui-btn layui-btn-xs layui-btn-success">每月巡检</button>
    {{# } }}
</script>

        </div>
    </div>

    
    <script type="text/javascript" src="/static/js/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="/static/layui/layui.js"></script>

    
    
    
<script>
    layui.use(['jquery', 'layer', 'form', 'table'], function () {
        var $ = layui.$,
            layer = layui.layer,
            form = layui.form,
            table = layui.table;

        // 时间戳的处理
        layui.laytpl.toDateString = function(d, format){
            var date = new Date(d || new Date())
                ,ymd = [
                this.digit(date.getFullYear(), 4)
                ,this.digit(date.getMonth() + 1)
                ,this.digit(date.getDate())
            ]
                ,hms = [
                this.digit(date.getHours())
                ,this.digit(date.getMinutes())
                ,this.digit(date.getSeconds())
            ];

            format = format || 'yyyy-MM-dd HH:mm:ss';

            return d ? format.replace(/yyyy/g, ymd[0])
                .replace(/MM/g, ymd[1])
                .replace(/dd/g, ymd[2])
                .replace(/HH/g, hms[0])
                .replace(/mm/g, hms[1])
                .replace(/ss/g, hms[2]) : '';
        };
        // 数字前置补零
        layui.laytpl.digit = function(num, length, end){
            var str = '';
            num = String(num);
            length = length || 2;
            for(var i = num.length; i < length; i++){
                str += '0';
            }
            return num < Math.pow(10, length) ? str + (num|0) : num;
        };

        // 渲染数据表格
        table.render({
            elem : '#dataTable'
            ,url : '<?php echo url("logList"); ?>'
            ,cellMinWidth: 80
            ,page: {
                prev: '上一页',
                next: '下一页',
                layout: ['prev', 'page', 'next', 'skip', 'count', 'limit']
            }
            // ,toolbar: 'default'  // 开启顶部工具栏（默认模板）
            ,toolbar: '#toolbar' // 指定顶部工具栏模板
            // ,even: true  // 隔行背景
            ,title: '质控日志表'  // 表格标题，用户导出数据文件名
            ,text: {  // 指定无数据或数据异常时的提示文本
                none: '暂无相关数据' //默认：无数据。注：该属性为 layui 2.2.5 开始新增
            }
            ,id: 'dataTable'
            ,cols: [[  // 表格列标题及数据
                {title: '#', type: 'numbers'}
                ,{checkbox: true}
                ,{field: 'id', width: 60, title: 'ID', sort: true, align: 'center'}
                ,{field: 'type', width: 200, title: '检查类型', sort: true, align: 'center', templet: '#type'}
                ,{field: 'operator', width: 150, title: '检查人员', align: 'center'}
                ,{field: 'qc_status', width: 100, title: '检查结果',align: 'center', templet: '#status'}
                ,{field: 'qc_time', width: 200, title: '检查时间', sort: true,  align: 'center'}
                ,{field: 'item', width: 150, title: '设备信息', align: 'center'}
                ,{field: 'location', width: 200, title: '设备位置', align: 'center'}
                ,{field: 'org_id', width: 200, title: '组织ID', align: 'center'}
                ,{field: 'memo', width: 200, title: '备注', align: 'center'}
                ,{fixed: 'right', width: 200, title: '操作', align:'center', toolbar: '#barTool'}
            ]], done() {
                // 搜索功能
                var $ = layui.$, active = {
                    reload: function(){
                        var keywords = $('#keywords');
                        //执行重载
                        table.reload('dataTable', {
                            page: {
                                curr: 1 //重新从第 1 页开始
                            }
                            ,where: {
                                keywords: keywords.val()
                            }
                        }, 'data');
                    }
                };
                $('.search-btn').on('click', function(){
                    var type = $(this).data('type');
                    active[type] ? active[type].call(this) : '';
                });
            }
        });

        // 监听行内工具栏
        table.on('tool(dataTable)', function (obj) {
            var e = obj.event;
            var data = obj.data;

            if ( e === 'del') {
                layer.confirm('您确定要删除吗？', {
                    icon: 3
                },function(index){
                    // 加载层
                    var loading = layer.msg('处理中，请稍后...', {
                        icon: 16,
                        shade: 0.5
                    });
                    $.ajax({
                        url: '<?php echo url("qualitylog/delete"); ?>',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            id : data.id
                        },
                        beforeSend: function(){},
                        success: function(res){
                            layer.close(loading);
                            if (res.status == 1) {
                                layer.msg(res.message, {
                                    time: 1000
                                },function(){
                                    window.location.reload();
                                });
                            } else if (res.status == 0) {
                                layer.alert(res.message, {
                                    icon: 2,
                                    skin: 'layer-ext-moon'
                                },function(){
                                    parent.window.location.reload();
                                    var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                                    parent.layer.close(index);
                                });
                            }
                        }
                    });
                });
            }
        });
    });
</script>

</body>
</html>