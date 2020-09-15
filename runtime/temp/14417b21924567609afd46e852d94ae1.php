<?php /*a:2:{s:77:"/Applications/MAMP/htdocs/operator/application/index/view/workorder/edit.html";i:1586306807;s:82:"/Applications/MAMP/htdocs/operator/application/index/view/public/layer_layout.html";i:1582010668;}*/ ?>
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
                    
<form class="layui-form" id="form1" lay-filter="component-form-element">
    <div class="layui-row layui-col-space10 layui-form-item">

        <div class="layui-form-item">
            <label class="layui-form-label">归属医院</label>
            <div class="layui-input-block">
                <select name="org_id" lay-filter="orgFilter">
                    {<?php if(is_array($orglist) || $orglist instanceof \think\Collection || $orglist instanceof \think\Paginator): $i = 0; $__LIST__ = $orglist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$org): $mod = ($i % 2 );++$i;?>}
                        <?php if($org['id']==$workorderInfo['org_id']): ?> 
                            <option value="<?php echo htmlentities($org['id']); ?>" selected><?php echo htmlentities($org['name']); ?></option>
                         <?php else: ?>
                             <option value="<?php echo htmlentities($org['id']); ?>"><?php echo htmlentities($org['name']); ?></option>
                        <?php endif; ?>
                    {<?php endforeach; endif; else: echo "" ;endif; ?>}
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">设备编码</label>
            <div class="layui-input-block">
                <select id="item_id" name="item_id">
                    <option value="<?php echo htmlentities($workorderInfo['item_id']); ?>"><?php echo htmlentities($workorderInfo['items']['catagory']['name']); ?>-<?php echo htmlentities($workorderInfo['items']['code']); ?>-<?php echo htmlentities($workorderInfo['items']['sn']); ?></option>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">设备地点：</label>
            <div class="layui-input-block">
                <input type="text" name="location" lay-verify="required" lay-reqText="产品目录名不能为空" placeholder="请输入设备地点" autocomplete="off" class="layui-input" value="<?php echo htmlentities($workorderInfo['location']); ?>">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">操作</label>
            <div class="layui-input-block">
                <select id="status" name="status">
                    <?php if($workorderInfo['status']==0): ?> 
                        <option value="1" selected>接修</option>
                        <option value="0" >未接修</option>

                    <?php endif; if($workorderInfo['status']==1): ?> 
                        <option value="2" selected>维修完成</option>
                         <option value="1" >维修中</option>

                    <?php endif; if($workorderInfo['status']==2): ?> 
                         <option value="2" selected>已完修</option>
                    <?php endif; ?>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">是否故障停机</label>
            <div class="layui-input-block">
                <select id="is_halt" name="is_halt">
                    <?php if($workorderInfo['is_halt']==0): ?> 
                    <option value="0" selected>否</option>
                    <option value="1">是</option>
                    <?php endif; if($workorderInfo['is_halt']==1): ?> 

                    <option value="1" selected>是</option>
                    <option value="0">否</option>
                    <?php endif; ?>
                </select>
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">停机时长(小时)：</label>
            <div class="layui-input-block">
                <input type="text" name="halt_time"  placeholder="请输入停机时长" autocomplete="off" class="layui-input" value="<?php echo htmlentities($workorderInfo['halt_time']); ?>">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">维修费用：</label>
            <div class="layui-input-block">
                <input type="text" name="cost"  placeholder="请输入维修费用" autocomplete="off" class="layui-input" value="<?php echo htmlentities($workorderInfo['cost']); ?>">
            </div>
        </div>
        <div class="layui-form-item">
            <label class="layui-form-label">报修备注：</label>
            <div class="layui-input-block">
                <textarea name="memo"  placeholder="请输入备注" class="layui-textarea" ><?php echo htmlentities($workorderInfo['notification']['memo']); ?></textarea>
            </div>
        </div>
        <input type="hidden" name="id" value="<?php echo htmlentities($workorderid); ?>">
        <input type="hidden" name="notification_id" value="<?php echo htmlentities($workorderInfo['notification_id']); ?>">
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
                url: '<?php echo url("doEdit"); ?>',
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
                    } else if (data.status == -1) {
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

        form.on('select(orgFilter)',function(data){

            //发送一个ajax
            $.ajax({
                url:'<?php echo url("getItem"); ?>',
                type:'post',
                data:{'org_id':data.value},
                dataType:'json',
                success:function(data){
                    // var data=(JSON.parse(data))['data'];

                    //获得数据更新之前先删除之前的旧数据
                    $('#item_id').html('');

                    //遍历增加option
                    data.forEach(element => {
                        $('#item_id').append('<option value ="'+element['id']+'">'+element['name']+'-'+element['code']+'-'+element['sn']+'</option>');
                    });

                    //表单重新渲染
                    form.render();
                }
            });
        });
    });
</script>

</body>
</html>