<?php /*a:2:{s:71:"/Applications/MAMP/htdocs/operator/application/admin/view/node/add.html";i:1582365431;s:82:"/Applications/MAMP/htdocs/operator/application/admin/view/public/layer_layout.html";i:1582010668;}*/ ?>
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
    <link rel="stylesheet" type="text/css" href="/static/css/admin/layuimini.css" />
    <link rel="stylesheet" type="text/css" href="/static/css/font-awesome-4.7.0/css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="/static/css/iconfont/iconfont.css" />
    <link rel="stylesheet" type="text/css" href="/static/css/admin/admin.css" />
    
</head>
<body id="layer">
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    
<form class="layui-form" id="form1">
    <div class="layui-row layui-col-space10 layui-form-item">
        <div class="layui-form-item">
            <label class="layui-form-label">上级节点：</label>
            <div class="layui-input-block">
                <input type="text" class="layui-input input-disable" disabled value="<?php echo htmlentities($parentNode); ?>">
                <input type="hidden" name="pid" value="<?php echo htmlentities($pid); ?>">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">节点名称：</label>
            <div class="layui-input-block">
                <input type="text" name="name" class="layui-input" placeholder="请输入节点名称" autocomplete="off" lay-verify="required" lay-reqText="节点名称不能为空">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">节点路径：</label>
            <div class="layui-input-block">
                <input type="text" name="path" class="layui-input" placeholder="控制器/方法" autocomplete="off" lay-verify="required" lay-reqText="节点路径不能为空">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">节点图标：</label>
            <div class="layui-input-block">
                <input type="text" name="icon" class="layui-input" id="icon" readonly placeholder="请选择节点图标" autocomplete="off">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">是否菜单：</label>
            <div class="layui-input-block">
                <input type="radio" name="is_menu" value="2" title="是" lay-verify="required">
                <input type="radio" name="is_menu" value="1" title="否" lay-verify="required" checked>
            </div>
        </div>
        <div class="layui-form-item layui-hide">
            <div class="layui-input-block">
                <button type="submit" class="layui-btn" id="submit" lay-submit lay-filter="submit">立即提交</button>
                <button type="reset" class="layui-btn layui-btn-primary" id="reset">重置</button>
            </div>
        </div>
</form>

                </div>
            </div>
        </div>
    </div>

    
    <script type="text/javascript" src="/static/js/jquery-3.4.1.min.js"></script>
    <script type="text/javascript" src="/static/layui/layui.js"></script>
    <script type="text/javascript" src="/static/js/admin/layout.js"></script>
    

    
    
    
    <script>
        layui.use(['element', 'layer', 'layuimini'], function () {
            var $ = layui.jquery,
                element = layui.element,
                layer = layui.layer;
            layuimini.init('');
        });
        function editPwd() {
            layer.open({
                type: 2,
                title: '修改密码',
                shade: 0.6,
                area: ['40%', '50%'],
                content: '<?php echo url("index/editPwd"); ?>'
            });
        }
    </script>
    
    
<script>
    layui.use(['form', 'jquery'], function () {
        var form = layui.form,
            $ = layui.$;

        $('#icon').on('click', function () {
            var index = layer.open({
                type: 2,
                title: '<i class="layui-icon layui-icon-radio"></i> 选择节点图标',
                area: ['505px', '350px'],
                content: ['<?php echo url("node/icon"); ?>', 'yes']
            });
        });

        form.on('submit(submit)',function(data){
            // 加载层
            var loading = layer.msg('处理中，请稍后...', {
                icon: 16,
                shade: 0.2
            });
            $.ajax({
                url: '<?php echo url("doAdd"); ?>',
                type: 'POST',
                dataType: 'json',
                data: $('#form1').serialize(),
                beforeSend: function(){},
                success: function(data){
                    layer.close(loading);
                    if (data.status == 1) {
                        layer.msg(data.message, {
                            time: 1500
                        },function(){
                            parent.window.location.reload();
                            var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                            parent.layer.close(index);
                        });
                    } else if (data.status == 2) {
                        layer.alert(data.message, {
                            icon: 0,
                            skin: 'layer-ext-moon'
                        }, function() {
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
    });
</script>

</body>
</html>