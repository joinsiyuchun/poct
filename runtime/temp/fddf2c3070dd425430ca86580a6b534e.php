<?php /*a:2:{s:61:"/var/www/html/operator/application/index/view/item/index.html";i:1600162329;s:64:"/var/www/html/operator/application/index/view/public/layout.html";i:1599656086;}*/ ?>
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
            

<!--<div class="layui-form" action="">-->
<table class="layui-hide" id="treeTable" lay-filter="treeTable"></table>
<!--</div>-->

<script type="text/html" id="toolbar">
    <div class="dataToolbar">
        <div class="layui-inline">
            <input class="layui-input" name="keywords" id="keywords" value="<?php echo input('keywords'); ?>" autocomplete="on"
                   placeholder="请输入设备编码">
        </div>
        <button class="layui-btn search-btn" data-type="reload"><i class="iconfont">&#xe679;</i> 查询</button>
        <button class="layui-btn" id="import" data-type="import"><i class="iconfont">&#xe692;</i> 导入设备档案
        </button>

        <div class="layui-inline">
            <?php if((buttonCheck('index/item/add'))): ?>
            <button class="layui-btn" id="add" data-pid="0"><i class="iconfont">&#xe692;</i> 添加顶层设备</button>
            <?php endif; if((buttonCheck('index/item/asign'))): ?>
            <button class="layui-btn" id="assign" data-pid="0"><i class="iconfont">&#xe692;</i> 分配组织</button>
            <?php endif; ?>
            <button class="layui-btn layui-btn-primary open-all" id="openAll"><i class="iconfont">&#xe9d6;</i> 全部关闭
            </button>
        </div>
    </div>
</script>


<script type="text/html" id="barTool">
    <?php if((buttonCheck('index/item/edit'))): ?>
    <a href="javascript:;" class="layui-btn layui-btn-xs" onclick="edit('{{d.id}}')"><i class="iconfont">&#xe7e0;</i> 编辑</a>
    <?php endif; if((buttonCheck('index/item/del'))): ?>
    <a href='javascript:;' class="layui-btn layui-btn-danger layui-btn-xs" onclick="del('{{d.id}}')"><i
            class="iconfont">&#xe6b4;</i> 删除</a>
    <?php endif; if((buttonCheck('index/item/getQrcode'))): ?>
    <a href="javascript:;" class="layui-btn layui-btn-xs" onclick="getQrcode('{{d.id}}')"><i
            class="iconfont">&#xe7e0;</i> 二维码</a>
    <?php endif; if((buttonCheck('index/item/assignOrg'))): ?>
    <a href="javascript:;" lay-event="assignOrg" class="layui-btn layui-btn-xs" onclick="assignOrg('{{d.id}}')"><i
            class="iconfont">&#xe7e0;</i>分配组织</a>
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


<script type="text/html" id="is_backup">
    <?php if(app('session')->get('admin_id') == '1'): ?>
    {{# if(d.is_backup == 1){ }}
    <button class="layui-btn layui-btn-xs layui-btn-danger " onclick="setBackup('{{d.id}}', '{{d.is_bakcup}}')">是
    </button>
    {{# } else { }}
    <button class="layui-btn layui-btn-xs layui-btn-success" onclick="setBackup('{{d.id}}', '{{d.is_backup}}')">否
    </button>
    {{# } }}
    <?php else: ?>
    {{# if(d.is_backup == 1){ }}
    <button class="layui-btn layui-btn-xs layui-btn-danger">是</button>
    {{# } else { }}
    <button class="layui-btn layui-btn-xs ">否</button>
    {{# } }}
    <?php endif; ?>
</script>


<script type="text/html" id="isMenu">
    {{# if (d.is_kit == 1) { }}
    <i class="layui-icon layui-icon-close text-fail"></i>
    {{# } else if (d.is_kit == 2) { }}
    <?php if((buttonCheck('index/item/addChild'))): ?>
    <a href="javascript:;" lay-event="addChild" class="layui-btn layui-btn-xs"
       onclick="addChild('{{d.id}}', '{{d.title}}')"><i class="iconfont">&#xe7e0;</i> 添加</a>
    <?php else: ?>
    <i class="layui-icon layui-icon-ok text-success"></i>
    <?php endif; ?>
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
        treeTable: 'treeGrid/treeGrid'
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
            url: "<?php echo url('item/itemList'); ?>",
            cellMinWidth: 100,
            treeId: 'id', //树形id字段名称
            treeUpId: 'pid', //树形父id字段名称
            treeShowName: 'title', //以树形式显示的字段
            height: 'full-140',
            isFilter: false,
            iconOpen: false, //是否显示图标【默认显示】
            isOpenDefault: false, //节点默认是展开还是折叠【默认展开】
            onDblClickRow: false, //去除双击事件
            cols: [
                [
                    {checkbox: true},
                    {field: 'id', align: 'center', width: 60, title: 'ID'},
                    {field: 'title', width: 150, title: '设备分类'},
                    {field: 'code', width: 90, title: '设备编码'},
                    {field: 'status', width: 100, align: 'center', title: '状态', templet: '#status'},
                    {field: 'is_backup', width: 100, align: 'center', title: '是否备机', templet: '#is_backup'},
                    {field: 'sn', width: 100, title: '序列号'},
                    {field: 'pn', width: 100, title: 'P/N'},
                    {field: 'is_kit', width: 100, align: 'center', title: '子组件', templet: '#isMenu'},
                    {fixed: 'right', align: 'center', width: 350, title: '操作', toolbar: '#barTool'}
                ]
            ],
            page: false
        });

        $('.search-btn').on('click', function () {

            var keywords = $('input[name="keywords"]').val();
            $.ajax({
                type: "post",
                url: "<?php echo url('itemList'); ?>",
                data: {keywords: keywords},
                dataType: 'json',
                success: function (data) {
                    // console.log(data.data);
                    var itemlist = data.data;
                    itemlist.forEach(function (e) {
                        var newtitle = "<font color='red'>" + e['title'] + "</font>";
                        treeGrid.updateRow(tableId, {id: e['id'], title: newtitle});
                    });


                }
            });
            return false;
        });

        // 展开关闭节点
        $('#openAll').click(function (e) {
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
                title: '<i class=iconfont>&#xe7c7;</i> 添加顶级节点',
                area: ['600px', '475px'],
                content: ['<?php echo url("item/add", ["pid" => 0, "parentitem" => "顶级节点"]); ?>', 'yes'],
                skin: 'layui-layer-molv',
                btn: ['立即提交', '重置'],
                btnAlign: 'c',
                yes: function (index, layero) {
                    var submit = layero.find('iframe').contents().find("#submit");// #subBtn为页面层提交按钮ID
                    submit.click();// 触发提交监听
                    return false;
                },
                btn2: function (index, layero) {
                    var reset = layero.find('iframe').contents().find("#reset");// #subBtn为页面层提交按钮ID
                    reset.click();// 触发重置按钮
                    return false;
                }
            });
        });
        // 分配机构
        $('#assign').on('click', function () {
            var checkStatus = treeGrid.checkStatus(tableId); //idTest 即为基础参数 id 对应的值
            var checkeddata = checkStatus.data;
            var arr = new Array();
            for (i = 0; i < checkeddata.length; i++) {
                arr[i] = checkeddata[i]["id"];
            }
            var str = arr.join("|");
            var index = layer.open({
                type: 2,
                title: '<i class=iconfont>&#xe7c7;</i> 编辑节点',
                area: ['600px', '475px'],
                content: ['<?php echo url("item/assignorg"); ?>?id=' + str, 'yes'],
                skin: 'layui-layer-molv',
                btn: ['保存', '取消'],
                btnAlign: 'c',
                yes: function (index, layero) {
                    var submit = layero.find('iframe').contents().find("#submit");// #subBtn为页面层提交按钮ID
                    submit.click();// 触发提交监听
                    return false;
                },
                btn2: function (index, layero) {
                    layer.close(index);
                }
            });
        });


        // 分配机构
        $('#import').on('click', function () {
            var index = layer.open({
                type: 2,
                title: '<i class=iconfont>&#xe7e0;</i> 导入设备档案',
                area: ['800px', '560px'],
                content: ['<?php echo url("upload2db/index"); ?>?type=' + 'bireport', 'yes'],
                skin: 'layui-layer-molv',
                btn: ['取消'],
                btnAlign: 'c',
                yes: function (index, layero) {
                    layer.close(index);
                }
            });
        });
    });

    // 添加子节点
    function addChild(pid, name) {
        var index = layer.open({
            type: 2,
            title: '<i class=iconfont>&#xe7c7;</i> 添加子节点',
            area: ['600px', '475px'],
            content: ['add/?pid=' + pid + '&parentitem=' + name, 'yes'],
            skin: 'layui-layer-molv',
            btn: ['立即提交', '重置'],
            btnAlign: 'c',
            yes: function (index, layero) {
                var submit = layero.find('iframe').contents().find("#submit");// #subBtn为页面层提交按钮ID
                submit.click();// 触发提交监听
                return false;
            },
            btn2: function (index, layero) {
                var reset = layero.find('iframe').contents().find("#reset");// #subBtn为页面层提交按钮ID
                reset.click();// 触发重置按钮
                return false;
            }
        });
    }

    // 添加子节点
    function assignOrg(id) {
        var index = layer.open({
            type: 2,
            title: '<i class=iconfont>&#xe7e0;</i> 分配组织',
            area: ['400px', '625px'],
            content: ['<?php echo url("item/assignSingleOrg"); ?>?id=' + id, 'yes'],
            skin: 'layui-layer-molv',
            btn: ['保存', '取消'],
            btnAlign: 'c',
            yes: function (index, layero) {
                var submit = layero.find('iframe').contents().find("#submit");// #subBtn为页面层提交按钮ID
                submit.click();// 触发提交监听
                return false;
            },
            btn2: function (index, layero) {
                layer.close(index);
            }
        });
    }

    // 下载二维码
    function getQrcode(id) {
        var index = layer.open({
            type: 1,
            title: '<i class=iconfont>&#xe7c7;</i> 二维码',
            skin: 'layui-layer-rim', //加上边框
            area: ['470px', '580px'], //宽高
            shadeClose: true, //开启遮罩关闭
            end: function (index, layero) {
                return false;
            },
            content: '<div style="text-align:center"><img src="' + "/static/uploads/" + id + '.jpg" /></div>'
        });
    }


    // 编辑节点
    function edit(id) {
        var index = layer.open({
            type: 2,
            title: '<i class=iconfont>&#xe7c7;</i> 编辑节点',
            area: ['600px', '475px'],
            content: ['<?php echo url("item/edit"); ?>?id=' + id, 'yes'],
            skin: 'layui-layer-molv',
            btn: ['保存', '取消'],
            btnAlign: 'c',
            yes: function (index, layero) {
                var submit = layero.find('iframe').contents().find("#submit");// #subBtn为页面层提交按钮ID
                submit.click();// 触发提交监听
                return false;
            },
            btn2: function (index, layero) {
                layer.close(index);
            }
        });
    }

    // 删除节点
    function del(id) {
        layer.confirm('您确定要删除该节点吗？', {
            icon: 3,
            skin: 'layer-ext-moon'
        }, function (index) {
            // 加载层
            var loading = layer.msg('处理中，请稍后...', {
                icon: 16,
                shade: 0.2
            });
            $.ajax({
                type: "post",
                url: "<?php echo url('item/delete'); ?>",
                data: {id: id},
                dataType: 'json',
                beforeSend: function () {
                },
                success: function (data) {
                    layer.close(loading);
                    if (data.status == 1) {
                        layer.msg(data.message, {
                            time: 1500
                        }, function () {
                            window.location.reload();
                        });
                    } else if (data.status == 0) {
                        layer.alert(data.message, {
                            icon: 2,
                            skin: 'layer-ext-moon'
                        }, function () {
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
            url: "<?php echo url('item/setStatus'); ?>",
            type: 'POST',
            dataType: 'json',
            data: {
                id: id,
                status: status
            },
            beforeSend: function () {
            },
            success: function (data) {
                layer.close(loading);
                if (data.status == 1) {
                    layer.msg(data.message, {
                        time: 1500
                    }, function () {
                        window.location.reload();
                    });
                } else {
                    layer.alert(data.message, {
                        icon: 2
                    }, function () {
                        window.location.reload();
                    });
                }
            }
        });
    }

    // 设置显示状态
    function setBackup(id, status) {
        // 加载层
        var loading = layer.msg('处理中，请稍后...', {
            icon: 16,
            shade: 0.2
        });
        $.ajax({
            url: "<?php echo url('item/setBackup'); ?>",
            type: 'POST',
            dataType: 'json',
            data: {
                id: id,
                status: status
            },
            beforeSend: function () {
            },
            success: function (data) {
                layer.close(loading);
                if (data.status == 1) {
                    layer.msg(data.message, {
                        time: 1500
                    }, function () {
                        window.location.reload();
                    });
                } else {
                    layer.alert(data.message, {
                        icon: 2
                    }, function () {
                        window.location.reload();
                    });
                }
            }
        });
    }
</script>

</body>
</html>