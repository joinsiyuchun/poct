<?php /*a:2:{s:62:"/var/www/html/operator/application/admin/view/group/index.html";i:1599656086;s:64:"/var/www/html/operator/application/admin/view/public/layout.html";i:1599656086;}*/ ?>
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
            <input class="layui-input" name="keywords" id="keywords" value="<?php echo input('keywords'); ?>" autocomplete="on" placeholder="请输入维修组名">
        </div>
        <button class="layui-btn search-btn" data-type="reload"><i class="iconfont">&#xe679;</i> 查询</button>
        <?php if((buttonCheck('admin/group/add'))): ?>
        <div class="layui-inline">
            <button class="layui-btn" id="add"><i class="iconfont">&#xe692;</i> 添加维修组</button>
        </div>
        <?php endif; ?>
    </div>
</script>


<script type="text/html" id="barTool">
    <?php if((buttonCheck('admin/group/edit'))): ?>
    <a href="javascript:;" lay-event="edit" class="layui-btn layui-btn-xs"><i class="iconfont">&#xe7e0;</i> 编辑</a>
    <?php endif; if((buttonCheck('admin/group/del'))): ?>
    {{# if(d.id == '1') { }}
    <a href='javascript:;' class="layui-btn layui-btn-disabled layui-btn-xs"><i class="iconfont">&#xe6b4;</i> 删除</a>
    {{# } else { }}
    <a href='javascript:;' lay-event="del" class="layui-btn layui-btn-danger layui-btn-xs"><i class="iconfont">&#xe6b4;</i> 删除</a>
    {{# } }}
    <?php endif; if((buttonCheck('admin/group/auth'))): ?>
    {{# if ( d.id == '1' ) { }}
    <a href="javascript:;" class="layui-btn layui-btn-xs layui-btn-disabled"><i class="iconfont">&#xe825;</i> 关联机构</a>
    {{# } else { }}
    <a href="javascript:;" lay-event="auth" class="layui-btn layui-btn-xs"><i class="iconfont">&#xe825;</i> 关联机构</a>
    {{# } }}
    <?php endif; ?>
</script>


<script type="text/html" id="status">
    <?php if(app('session')->get('admin_id') == '1'): ?>
    {{# if(d.status == 1){ }}
    <button class="layui-btn layui-btn-xs" onclick="setStatus('{{d.id}}', '{{d.status}}')">启用</button>
    {{# } else { }}
    <button class="layui-btn layui-btn-xs layui-btn-danger" onclick="setStatus('{{d.id}}', '{{d.status}}')">禁用</button>
    {{# } }}
    <?php else: ?>
    {{# if(d.status == 1){ }}
    <button class="layui-btn layui-btn-xs">启用</button>
    {{# } else { }}
    <button class="layui-btn layui-btn-xs layui-btn-danger">禁用</button>
    {{# } }}
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
            ,url : '<?php echo url("groupList"); ?>'
            ,cellMinWidth: 80
            ,page: {
                prev: '上一页',
                next: '下一页',
                layout: ['prev', 'page', 'next', 'skip', 'count', 'limit']
            }
            // ,toolbar: 'default'  // 开启顶部工具栏（默认模板）
            ,toolbar: '#toolbar' // 指定顶部工具栏模板
            // ,even: true  // 隔行背景
            ,title: '维修组表'  // 表格标题，用户导出数据文件名
            ,text: {  // 指定无数据或数据异常时的提示文本
                none: '暂无相关数据' //默认：无数据。注：该属性为 layui 2.2.5 开始新增
            }
            ,id: 'dataTable'
            ,cols: [[  // 表格列标题及数据
                {title: '#', type: 'numbers'}
                ,{checkbox: true}
                ,{field: 'id', width: 60, title: 'ID', sort: true, align: 'center'}
                ,{field: 'name', width: 200, title: '维修组', align: 'center'}
                ,{field: 'status', width: 80, title: '状态',align: 'center', templet: '#status'}
                ,{field: 'create_time', width: 200, title: '创建时间', sort: true,  align: 'center'}
                ,{field: 'update_time', width: 200, title: '更新时间', sort: true,  align: 'center'}
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
                    title: '<i class=iconfont>&#xe7e0;</i> 编辑维修组',
                    area: ['500px', '400px'],
                    content: ['<?php echo url("group/edit"); ?>?id=' + data.id, 'yes'],
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
                        url: '<?php echo url("group/del"); ?>',
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
                    title: '<i class=iconfont>&#xe7e0;</i> 关联规则',
                    area: ['400px', '625px'],
                    content: ['<?php echo url("group/auth"); ?>?id=' + data.id, 'yes'],
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

        // 添加维修组
        $('#add').on('click', function () {
            var index = layer.open({
                type: 2,
                title: '<i class=iconfont>&#xe7c7;</i> 添加维修组',
                area: ['500px', '400px'],
                content: ['<?php echo url("group/add"); ?>', 'yes'],
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

    // 设置显示状态
    function setStatus(id, status) {
        // 加载层
        var loading = layer.msg('处理中，请稍后...', {
            icon: 16,
            shade: 0.2
        });
        $.ajax({
            url: "<?php echo url('group/setStatus'); ?>",
            type: 'POST',
            dataType: 'json',
            data: {
                id : id,
                status : status
            },
            beforeSend: function(){},
            success: function (data) {
                layer.close(loading);
                if (data.status == 1) {
                    layer.msg(data.message, {
                        time: 1500
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
    }
</script>

</body>
</html>