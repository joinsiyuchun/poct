<?php /*a:2:{s:71:"/Applications/MAMP/htdocs/operator/application/index/view/item/add.html";i:1598686179;s:82:"/Applications/MAMP/htdocs/operator/application/index/view/public/layer_layout.html";i:1582010668;}*/ ?>
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
                <input type="text" class="layui-input input-disable" disabled value="<?php echo htmlentities($parentItem); ?>">
                <input type="hidden" name="pid" value="<?php echo htmlentities($pid); ?>">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">设备分类</label>
            <div class="layui-input-block">
                <select id="catagoryid" name="catagoryid" lay-filter="catagoryFilter">
                    <option value="">请选择</option>
                    {<?php if(is_array($catagorylist) || $catagorylist instanceof \think\Collection || $catagorylist instanceof \think\Paginator): $i = 0; $__LIST__ = $catagorylist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$catagory): $mod = ($i % 2 );++$i;?>}
                        <option value="<?php echo htmlentities($catagory['id']); ?>"><?php echo htmlentities($catagory['name']); ?></option>
                    {<?php endforeach; endif; else: echo "" ;endif; ?>}
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">设备编码：</label>
            <div class="layui-input-block">
                <input type="text" name="code" class="layui-input" placeholder="请输入设备编码" autocomplete="off" lay-verify="required" lay-reqText="设备编码不能为空">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">品牌：</label>
            <div class="layui-input-block">
                <input type="text" name="brand" class="layui-input" placeholder="请输入品牌" autocomplete="off" lay-verify="required" lay-reqText="品牌不能为空">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">型号：</label>
            <div class="layui-input-block">
                <input type="text" name="model" class="layui-input" placeholder="请输入型号" autocomplete="off" lay-verify="required" lay-reqText="型号不能为空">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">序列号：</label>
            <div class="layui-input-block">
                <input type="text" name="sn" class="layui-input" placeholder="请输入序列号" autocomplete="off" lay-verify="required" lay-reqText="序列号不能为空">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">生产编号：</label>
            <div class="layui-input-block">
                <input type="text" name="pn" class="layui-input" placeholder="请输入生产编号" autocomplete="off" lay-verify="required" lay-reqText="生产编号不能为空">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">采购金额：</label>
            <div class="layui-input-block">
                <input type="text" name="purchase_price" class="layui-input" placeholder="请输入采购金额" autocomplete="off" lay-verify="required" lay-reqText="采购金额不能为空">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">启用日期：</label>
            <div class="layui-input-block">
                <input type="date" name="start_date" class="layui-input" placeholder="请输入启用日期" autocomplete="off" lay-verify="date" lay-reqText="启用日期不能为空">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">位置：</label>
            <div class="layui-input-block">
                <input type="text" name="location" class="layui-input" placeholder="请输入位置信息" autocomplete="off" lay-verify="required" lay-reqText="位置信息不能为空">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">是否备机：</label>
            <div class="layui-input-block">
                <input type="radio" name="is_backup" value="1" title="是" lay-verify="required">
                <input type="radio" name="is_backup" value="0" title="否" lay-verify="required" checked>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">存在子组件：</label>
            <div class="layui-input-block">
                <input type="radio" name="is_kit" value="2" title="是" lay-verify="required">
                <input type="radio" name="is_kit" value="1" title="否" lay-verify="required" checked>
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