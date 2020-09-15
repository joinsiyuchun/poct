<?php /*a:2:{s:75:"/Applications/MAMP/htdocs/operator/application/index/view/bireport/mri.html";i:1598713330;s:76:"/Applications/MAMP/htdocs/operator/application/index/view/public/layout.html";i:1582011447;}*/ ?>
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
            <input class="layui-input" name="keywords" id="keywords" value="<?php echo input('keywords'); ?>" autocomplete="on" placeholder="请输入设备编码">
        </div>
        <button class="layui-btn search-btn" data-type="reload"><i class="iconfont">&#xe679;</i> 查询</button>
    </div>
</script>


<script type="text/html" id="barTool">
    <a href="javascript:;" lay-event="benefit" class="layui-btn layui-btn-xs">收入</a>
    <a href="javascript:;" lay-event="cost" class="layui-btn layui-btn-xs">成本</a>
    <a href="javascript:;" lay-event="usage" class="layui-btn layui-btn-xs">使用率</a>
    <a href='javascript:;' lay-event="downtime" class="layui-btn layui-btn-danger layui-btn-xs">故障率</a>

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
            ,url : '<?php echo url("item/itemListbymri"); ?>'
            ,cellMinWidth: 80
            ,page: {
                prev: '上一页',
                next: '下一页',
                layout: ['prev', 'page', 'next', 'skip', 'count', 'limit']
            }
            // ,toolbar: 'default'  // 开启顶部工具栏（默认模板）
            ,toolbar: '#toolbar' // 指定顶部工具栏模板
            // ,even: true  // 隔行背景
            ,title: 'CT效率效益分析表'  // 表格标题，用户导出数据文件名
            ,text: {  // 指定无数据或数据异常时的提示文本
                none: '暂无相关数据' //默认：无数据。注：该属性为 layui 2.2.5 开始新增
            }
            ,id: 'dataTable'
            ,cols: [[  // 表格列标题及数据
                {title: '#', type: 'numbers'}
                ,{checkbox: true}
                ,{field: 'id', width: 60, title: 'ID', sort: true, align: 'center'}
                ,{field: 'title', width: 150, title: '设备分类', align: 'center'}
                ,{field: 'code', width: 100, title: '设备编码',align: 'center'}
                ,{field: 'brand', width: 200, title: '品牌', align: 'center'}
                ,{field: 'model', width: 200, title: '型号', align: 'center'}
                ,{field: 'location', width: 200, title: '房间', sort: true,  align: 'center'}
                ,{field: 'status', width: 150, title: '状态', align: 'center'}
                ,{field: 'is_backup', width: 200, title: '是否备机', align: 'center'}
                ,{field: 'purchase_price', width: 150, title: '采购价', align: 'center'}
                ,{field: 'start_date', width: 200, title: '启用日期', align: 'center'}
                ,{field: 'sn', width: 200, title: '序列号', align: 'center'}
                ,{field: 'pn', width: 200, title: 'P/N', align: 'center'}
                ,{fixed: 'right', width: 300, title: '操作', align:'center', toolbar: '#barTool'}
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

            if ( e === 'benefit') {
                var index = layer.open({
                    type: 2,
                    title: '<i class=iconfont>&#xe7e0;</i> 收入趋势分析',
                    area: ['800px', '560px'],
                    content: ['<?php echo url("bireport/benefit"); ?>?id=' + data.id, 'yes'],
                    skin: 'layui-layer-molv',
                    btn: [ '取消'],
                    btnAlign: 'c',
                    yes: function(index, layero){
                        layer.close(index);
                    }
                });
            }else if ( e === 'cost') {
                var index = layer.open({
                    type: 2,
                    title: '<i class=iconfont>&#xe7e0;</i> 成本趋势分析',
                    area: ['800px', '560px'],
                    content: ['<?php echo url("bireport/cost"); ?>?id=' + data.id, 'yes'],
                    skin: 'layui-layer-molv',
                    btn: [ '取消'],
                    btnAlign: 'c',
                    yes: function(index, layero){
                        layer.close(index);
                    }
                });
            }else if ( e === 'usage') {
                var index = layer.open({
                    type: 2,
                    title: '<i class=iconfont>&#xe7e0;</i> 成本趋势分析',
                    area: ['800px', '560px'],
                    content: ['<?php echo url("bireport/usage"); ?>?id=' + data.id, 'yes'],
                    skin: 'layui-layer-molv',
                    btn: [ '取消'],
                    btnAlign: 'c',
                    yes: function(index, layero){
                        layer.close(index);
                    }
                });
            }else if ( e === 'downtime') {
                var index = layer.open({
                    type: 2,
                    title: '<i class=iconfont>&#xe7e0;</i> 成本趋势分析',
                    area: ['800px', '560px'],
                    content: ['<?php echo url("bireport/downtime"); ?>?id=' + data.id, 'yes'],
                    skin: 'layui-layer-molv',
                    btn: [ '取消'],
                    btnAlign: 'c',
                    yes: function(index, layero){
                        layer.close(index);
                    }
                });
            }
        });
    });
</script>

</body>
</html>