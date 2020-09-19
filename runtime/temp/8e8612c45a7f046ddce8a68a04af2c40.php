<?php /*a:2:{s:60:"/var/www/html/operator/application/admin/view/org/index.html";i:1599656086;s:64:"/var/www/html/operator/application/admin/view/public/layout.html";i:1599656086;}*/ ?>
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
            

<!--<div class="layui-form">-->
    <table class="layui-hide" id="treeTable" lay-filter="treeTable"></table>
<!--</div>-->

<script type="text/html" id="toolbar">
    <div class="dataToolbar">
        <div class="layui-inline">
            <input class="layui-input" name="keywords" id="keywords" value="<?php echo input('keywords'); ?>" autocomplete="on" placeholder="请输入节点名">
        </div>
        <button class="layui-btn search-btn" data-type="reload"><i class="iconfont">&#xe679;</i> 查询</button>

        <div class="layui-inline">
            <?php if((buttonCheck('org/add'))): ?>
            <button class="layui-btn" id="add" data-pid="0"><i class="iconfont">&#xe692;</i> 添加机构</button>
            <?php endif; ?>
            <button class="layui-btn layui-btn-primary open-all" id="openAll"><i class="iconfont">&#xe9d6;</i> 全部关闭</button>
        </div>
    </div>
</script>


<script type="text/html" id="barTool">
    <?php if((buttonCheck('org/edit'))): ?>
    <a href="javascript:;" class="layui-btn layui-btn-xs" onclick="edit('{{d.id}}')"><i class="iconfont">&#xe7e0;</i> 编辑</a>
    <?php endif; if((buttonCheck('org/del'))): ?>
    <a href='javascript:;' class="layui-btn layui-btn-danger layui-btn-xs" onclick="del('{{d.id}}')"><i class="iconfont">&#xe6b4;</i> 删除</a>
    <?php endif; if((buttonCheck('org/addChild'))): ?>
    <a href="javascript:;" lay-event="addChild" class="layui-btn layui-btn-xs" onclick="addChild('{{d.id}}', '{{d.title}}')"><i class="iconfont">&#xe7e0;</i> 添加部门</a>
    <?php endif; ?>
</script>


<script type="text/html" id="status">
    <?php if(app('session')->get('admin_id') == '1'): ?>
    {{# if(d.status == 1){ }}
    <button class="layui-btn layui-btn-xs layui-btn-success" onclick="setStatus('{{d.id}}', '{{d.status}}')">启用</button>
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


<script type="text/html" id="isMenu">
    {{# if (d.is_menu == 1) { }}
    <i class="layui-icon layui-icon-close text-fail"></i>
    {{# } else if (d.is_menu == 2) { }}
    <i class="layui-icon layui-icon-ok text-success"></i>
    {{# } }}
</script>

        </div>
    </div>

    
    <script type="text/javascript" src="/static/js/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="/static/layui/layui.js"></script>

    
    
<script>
    layui.config({
        version: '1061458899',
        base: '/static/layui_exts/treeGrid/'
    }).extend({
        treeTable : 'treeGrid/treeGrid'
    });
</script>

    
<script>
    layui.use(['jquery', 'layer', 'form', 'table', 'treeGrid'], function () {
        var o = layui.$,
            layer = layui.layer,
            form = layui.form,
            table = layui.table,
            treeGrid = layui.treeGrid;
        var tableId = 'treeTable',
            mark = true;

        treeTable = treeGrid.render({
            id: tableId,
            elem: '#' + tableId,
            toolbar: '#toolbar',
            idField: 'id',
            url: "<?php echo url('orgList'); ?>",
            cellMinWidth: 100,
            treeId: 'id', //树形id字段名称
            treeUpId: 'pid', //树形父id字段名称
            treeShowName: 'title', //以树形式显示的字段
            height: 'full-140',
            isFilter: false,
            iconOpen: false, //是否显示图标【默认显示】
            isOpenDefault: true, //节点默认是展开还是折叠【默认展开】
            onDblClickRow: false, //去除双击事件
            cols: [
                [
                    { checkbox: true },
                    { field: 'id', align: 'center', width: 60, title: 'ID' },
                    { field: 'title', width: 200, title: '机构名称'},
                    { field: 'status', width: 100, align: 'center', title: '状态', templet: '#status'},
                    { fixed: 'right', align: 'center', width: 250, title: '操作', toolbar: '#barTool' }
                ]
            ],
            page: false
        });

        $('.search-btn').on('click', function(){
            var keywords = $('input[name="keywords"]').val();
            $.ajax({
                type: "post",
                url: "<?php echo url('org/orgList'); ?>",
                data: {keywords : keywords},
                dataType: 'json',
                beforeSend: function(){},
                success: function (data) {
                    // treeTable = treeGrid.reload();
                    console.log(data.data);
                    var itemlist=data.data;
                    itemlist.forEach(function(e) {
                        var newtitle="<font color=\"red\">"+e['title']+"</font>"
                        treeGrid.updateRow(tableId,{id:e['id'],title:newtitle});
                    });
                }
            });
            return false;
        });

        // 展开关闭节点
        $('#openAll').click(function(e) {
            var treedata = treeGrid.getDataTreeList(tableId);
            treeGrid.treeOpenAll(tableId, !treedata[0][treeGrid.config.cols.isOpen]);
            mark = !mark;
            mark ? $(this).html('<i class="iconfont">&#xe9d6;</i> 全部关闭') : $(this).html('<i class="iconfont">&#xe9d7;</i> 全部展开');
        });

        // 添加顶级节点
        $('#add').on('click', function () {
            var pid = $(this).attr('data-pid');
            var index = layer.open({
                type: 2,
                title: '<i class=iconfont>&#xe7c7;</i> 添加机构',
                area: ['600px', '475px'],
                content: ['<?php echo url("org/add", ["pid" => 0, "parentorg" => "顶级节点"]); ?>', 'yes'],
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

    // 添加子节点
    function addChild(pid, name) {
        var index = layer.open({
            type: 2,
            title: '<i class=iconfont>&#xe7c7;</i> 添加下属部门',
            area: ['600px', '475px'],
            content: ['add/?pid=' + pid + '&parentorg=' + name, 'yes'],
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
    }

    // 编辑节点
    function edit(id) {
        var index = layer.open({
            type: 2,
            title: '<i class=iconfont>&#xe7c7;</i> 编辑节点',
            area: ['600px', '475px'],
            content: ['<?php echo url("org/edit"); ?>?id=' + id, 'yes'],
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

    // 删除节点
    function del(id) {
        layer.confirm('您确定要删除该节点吗？',{
            icon: 3,
            skin: 'layer-ext-moon'
        },function(index){
            // 加载层
            var loading = layer.msg('处理中，请稍后...', {
                icon: 16,
                shade: 0.2
            });
            $.ajax({
                type: "post",
                url: "<?php echo url('org/delete'); ?>",
                data: {id : id},
                dataType: 'json',
                beforeSend: function(){},
                success: function (data) {
                    layer.close(loading);
                    if (data.status == 1) {
                        layer.msg(data.message, {
                            time: 1500
                        },function(){
                            window.location.reload();
                        });
                    } else if (data.status == 0) {
                        layer.alert(data.message, {
                            icon: 2,
                            skin: 'layer-ext-moon'
                        },function(){
                            window.location.reload();
                        });
                    }
                }
            });
            return false;
        });
    }

    // 设置显示状态
    function setStatus(id, status) {
        // 加载层
        var loading = layer.msg('处理中，请稍后...', {
            icon: 16,
            shade: 0.2
        });
        $.ajax({
            url: "<?php echo url('org/setStatus'); ?>",
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