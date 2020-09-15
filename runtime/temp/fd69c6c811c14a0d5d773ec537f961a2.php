<?php /*a:2:{s:65:"/var/www/html/operator/application/index/view/diaginfo/index.html";i:1599656086;s:64:"/var/www/html/operator/application/index/view/public/layout.html";i:1599656086;}*/ ?>
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
            <input class="layui-input" name="keywords" id="keywords" value="<?php echo input('keywords'); ?>" autocomplete="on" placeholder="请输入检查单号">
        </div>
        <button class="layui-btn search-btn" data-type="reload"><i class="iconfont">&#xe679;</i> 查询</button>

    </div>
</script>


<script type="text/html" id="barTool">
    <?php if((buttonCheck('index/diaginfo/del'))): ?>

    <a href='javascript:;' lay-event="del" class="layui-btn layui-btn-danger layui-btn-xs"><i class="iconfont">&#xe6b4;</i> 删除</a>
    
    <?php endif; ?>

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

        // 渲染数据表格
        table.render({
            elem : '#dataTable'
            ,url : '<?php echo url("diaginfoList"); ?>'
            ,cellMinWidth: 80
            ,page: {
                prev: '上一页',
                next: '下一页',
                layout: ['prev', 'page', 'next', 'skip', 'count', 'limit']
            }
            // ,toolbar: 'default'  // 开启顶部工具栏（默认模板）
            ,toolbar: '#toolbar' // 指定顶部工具栏模板
            // ,even: true  // 隔行背景
            ,title: '检查单信息表'  // 表格标题，用户导出数据文件名
            ,text: {  // 指定无数据或数据异常时的提示文本
                none: '暂无相关数据' //默认：无数据。注：该属性为 layui 2.2.5 开始新增
            }
            ,id: 'dataTable'
            ,cols: [[  // 表格列标题及数据
                {field: 'id', width: 200, title: 'ID', sort: true, align: 'center'}
                ,{field: 'request_id', width: 200, title: '检查单号', align: 'center'}
                ,{field: 'patient_source', width: 200, title: '患者来源', sort: true,  align: 'center'}
                ,{field: 'item_name', width: 200, title: '设备名称', sort: true,  align: 'center'}
                ,{field: 'item_id', width: 200, title: '设备编码', sort: true,  align: 'center'}
                ,{field: 'diag_name', width: 200, title: '检查项目', sort: true,  align: 'center'}
                ,{field: 'function', width: 80, title: '功能',align: 'center', sort: true, templet: '#status'}
                ,{field: 'part', width: 200, title: '检查部位', sort: true,  align: 'center'}
                ,{field: 'department', width: 200, title: '开单科室', sort: true,  align: 'center'}
                ,{field: 'profit', width: 200, title: '金额', sort: true,  align: 'center'}
                ,{field: 'is_positive', width: 200, title: '是否阳性', sort: true,  align: 'center'}
                ,{field: 'prescribe_date', width: 200, title: '医嘱开具时间', sort: true,  align: 'center'}
                ,{field: 'inspection_date', width: 200, title: '检查开始时间', sort: true,  align: 'center'}
                ,{field: 'report_date', width: 200, title: '报告审核完成时间', sort: true,  align: 'center'}
                ,{fixed: 'right', width: 250, title: '操作', align:'center', toolbar: '#barTool'}
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

            if ( e === 'edit' ) {
                var index = layer.open({
                    type: 2,
                    title: '<i class=iconfont>&#xe7e0;</i> 编辑报修工单信息',
                    area: ['500px', '400px'],
                    content: ['<?php echo url("diaginfo/edit"); ?>?id=' + data.id, 'yes'],
                    skin: 'layui-layer-molv',
                    btn: ['保存', '取消'],
                    btnAlign: 'c',
                    yes: function(index, layero){
                        var submit = layero.find('iframe').contents().find("#submit");// #subBtn为页面层提交按钮ID
                        submit.click();// 触发提交监听
                        return false;
                    },
                    btn2:function (index,layero) {
                        layer.close(index);
                    }
                });
            } else if ( e === 'del') {
                layer.confirm('您确定要删除吗？', {
                    icon: 3
                },function(index){
                    // 加载层
                    var loading = layer.msg('处理中，请稍后...', {
                        icon: 16,
                        shade: 0.5
                    });
                    $.ajax({
                        url: '<?php echo url("diaginfo/del"); ?>',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            id : data.id
                        },
                        beforeSend: function(){},
                        success: function(data){
                            layer.close(loading);
                            if (data.status == 1) {
                                layer.msg(data.message, {
                                    time: 1500
                                },function(){
                                    window.location.reload();
                                });
                            } else if ( data.status == -1 ) {
                                layer.alert(data.message, {
                                    icon: 2
                                },function(){
                                    window.location.reload();
                                });
                            } else {
                                layer.alert(data.message, {
                                    icon: 2
                                },function(){
                                    window.location.reload();
                                });
                            }
                        }
                    });
                });
            }else if ( e === 'auth' ) {
                var index = layer.open({
                    type: 2,
                    title: '<i class=iconfont>&#xe7e0;</i> 关联机构',
                    area: ['400px', '625px'],
                    content: ['<?php echo url("diaginfo/auth"); ?>?id=' + data.id, 'yes'],
                    skin: 'layui-layer-molv',
                    btn: ['保存', '取消'],
                    btnAlign: 'c',
                    yes: function(index, layero){
                        var submit = layero.find('iframe').contents().find("#submit");// #subBtn为页面层提交按钮ID
                        submit.click();// 触发提交监听
                        return false;
                    },
                    btn2:function (index,layero) {
                        layer.close(index);
                    }
                });
            }
        });

        // 添加角色
        $('#add').on('click', function () {
            var index = layer.open({
                type: 2,
                title: '<i class=iconfont>&#xe7c7;</i> 新建报修',
                area: ['500px', '400px'],
                content: ['<?php echo url("diaginfo/add"); ?>', 'yes'],
                skin: 'layui-layer-molv',
                btn: ['立即提交', '重置'],
                btnAlign: 'c',
                yes: function(index, layero){
                    var submit = layero.find('iframe').contents().find("#submit");// #subBtn为页面层提交按钮ID
                    submit.click();// 触发提交监听
                    return false;
                },
                btn2:function (index,layero) {
                    var reset = layero.find('iframe').contents().find("#reset");// #subBtn为页面层提交按钮ID
                    reset.click();// 触发重置按钮
                    return false;
                }
            });
        });
    });


</script>

</body>
</html>