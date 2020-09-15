<?php /*a:2:{s:81:"/Applications/MAMP/htdocs/operator/application/index/view/workorder/checkout.html";i:1583489083;s:82:"/Applications/MAMP/htdocs/operator/application/index/view/public/layer_layout.html";i:1582010668;}*/ ?>
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
    

<link rel="stylesheet" type="text/css" href="/static/layui_exts/ztree/metroStyle.css" />
</head>
<body id="layer">
<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-body">
                    
<div class="auth-title">
    结算订单ID：<span class="text-fail text-bold" id="test"><?php echo htmlentities($ids); ?></span>
</div>
<form class="layui-form" id="form1" lay-filter="component-form-element">
    <div class="layui-row layui-col-space10 layui-form-item">
        <div class="layui-form-item">
            <label class="layui-form-label">结算主体</label>
            <div class="layui-input-block">
                <select name="org_id" lay-filter="orgFilter" lay-verify="required" lay-reqText="结算主体不能为空">
                    <option value="">请选择</option>
                    {<?php if(is_array($orglist) || $orglist instanceof \think\Collection || $orglist instanceof \think\Paginator): $i = 0; $__LIST__ = $orglist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$org): $mod = ($i % 2 );++$i;?>}
                    <option value="<?php echo htmlentities($org['id']); ?>"><?php echo htmlentities($org['name']); ?></option>
                    {<?php endforeach; endif; else: echo "" ;endif; ?>}
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">结算金额：</label>
            <div class="layui-input-block">
                <input type="text" name="amount" lay-verify="required" lay-reqText="金额不能为空" placeholder="请输入结算金额" autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">备注：</label>
            <div class="layui-input-block">
                <textarea name="memo"  placeholder="请输入备注" class="layui-textarea"></textarea>

            </div>
        </div>
        <input type="hidden" name="ids" value="<?php echo htmlentities($ids); ?>">
        <div class="layui-form-item layui-hide">
            <div class="layui-input-block">
                <button type="submit" class="layui-btn" id="submit" lay-submit lay-filter="submit">立即提交</button>
                <button type="reset" class="layui-btn layui-btn-primary" id="reset">重置</button>
            </div>
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
layui.config({
    version: '1061458899',
    base: '/static/layui_exts/'
}).extend({
    ztree : 'ztree/ztree'
});
</script>

    
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
                url: '<?php echo url("doCheckout"); ?>',
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