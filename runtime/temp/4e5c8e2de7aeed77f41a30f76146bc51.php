<?php /*a:2:{s:78:"/Applications/MAMP/htdocs/operator/application/index/view/pricelist/index.html";i:1599634649;s:76:"/Applications/MAMP/htdocs/operator/application/index/view/public/layout.html";i:1582011447;}*/ ?>
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
            <input class="layui-input" name="keywords" id="keywords" value="<?php echo input('keywords'); ?>" autocomplete="on" placeholder="请输入医保编号">
        </div>
        <button class="layui-btn search-btn" data-type="reload"><i class="iconfont">&#xe679;</i> 查询</button>

    </div>
</script>


<script type="text/html" id="barTool">

    <a href='javascript:;' lay-event="del" class="layui-btn layui-btn-danger layui-btn-xs"><i class="iconfont">&#xe6b4;</i> 删除</a>

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
            ,url : '<?php echo url("pricelist"); ?>'
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
                ,{field: 'insurance_code', width: 200, title: '医保编码', align: 'center'}
                ,{field: 'status', width: 200, title: '状态', sort: true,  align: 'center'}
                ,{field: 'start_date', width: 200, title: '生效日期', sort: true,  align: 'center'}
                ,{field: 'expire_date', width: 200, title: '过期日期', sort: true,  align: 'center'}
                ,{field: 'price_code', width: 200, title: '收费编码', sort: true,  align: 'center'}
                ,{field: 'payment_method', width: 80, title: '支付方式',align: 'center', sort: true, templet: '#status'}
                ,{field: 'item_name', width: 200, title: '项目名称', sort: true,  align: 'center'}
                ,{field: 'item_desc', width: 200, title: '项目描述', sort: true,  align: 'center'}
                ,{field: 'exception', width: 200, title: '除外内容', sort: true,  align: 'center'}
                ,{field: 'pcs', width: 200, title: '单位', sort: true,  align: 'center'}
                ,{field: 'unit_price', width: 200, title: '项目单价', sort: true,  align: 'center'}
                ,{field: 'coverage', width: 200, title: '限定内容', sort: true,  align: 'center'}
                ,{field: 'catagory', width: 200, title: '费用类别', sort: true,  align: 'center'}
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
                        url: '<?php echo url("pricelist/del"); ?>',
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
            }
        });
    });


</script>

</body>
</html>