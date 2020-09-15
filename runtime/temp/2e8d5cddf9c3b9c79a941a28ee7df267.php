<?php /*a:2:{s:77:"/Applications/MAMP/htdocs/operator/application/index/view/item/assignorg.html";i:1582787335;s:82:"/Applications/MAMP/htdocs/operator/application/index/view/public/layer_layout.html";i:1582010668;}*/ ?>
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
    当前授权设备ID：<span class="text-fail text-bold" id="test"><?php echo htmlentities($ids); ?></span>
</div>
<form method="post" class="layui-form form-inline" id="form1">
    <div class="auth-list">
        <div class="zTreeDemoBackground left">
            <ul class="ztree" style="margin-left: 5px;margin-top:5px; padding: 0;">
                <li><a title="全部展开、折叠 "><span class="button ico_open"></span><span id="ztree_expandAll" data-open="false">全部展开、折叠 </span></a> </li>
            </ul>
            <ul id="treeDemo" class="ztree"></ul>
        </div>
        <input type="hidden" name="org_list" value="" />
        <input type="hidden" name="ids" value="<?php echo htmlentities($ids); ?>">
    </div>
    <div class="layui-form-item layui-hide">
        <div class="layui-input-inline">
            <button type="submit" class="layui-btn" id="submit" lay-submit lay-filter="submit"><i class="iconfont">&#xe645;</i> 立即提交</button>
            <button type="button" class="layui-btn layui-btn-normal" onclick="javascript:history.back(-1);"><i class="iconfont">&#xe9af;</i> 取消</button>
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
    layui.use(['form', 'jquery', 'ztree'], function () {
        var form = layui.form,
            $ = layui.$,
            ztree = layui.ztree;

        // 配置
        var setting = {
            //设置 zTree 的节点上是否显示 checkbox / radio
            check: {
                enable: true,
                chkboxType: { "Y": "ps", "N": "ps" }
            },
            data: {
                simpleData: {
                    enable: true,
                    idKey: "nid",
                    pIdKey: "parentid"
                }
            },
            callback: {
                beforeClick: function(treeId, treeNode) {
                    if (treeNode.isParent) {
                        zTree.expandNode(treeNode);
                        return false;
                    } else {
                        return true;
                    }
                },
                onClick: function(event, treeId, treeNode) {
                    // 节点ID
                    // var id = treeNode.id;
                    //保存当前点击的节点ID
                    // setCookie('tree_id', id, 1);
                }
            }
        };
        //节点数据
        var zNodes = <?php echo $json; ?>;

        //zTree对象
        var zTree = null;
        $(document).ready(function() {
            $.fn.zTree.init($("#treeDemo"), setting, zNodes);
            zTree = $.fn.zTree.getZTreeObj("treeDemo");
            zTree.expandAll(true);
            $("#ztree_expandAll").click(function() {
                if ($(this).data("open")) {
                    zTree.expandAll(false);
                    $(this).data("open", false);
                } else {
                    zTree.expandAll(true);
                    $(this).data("open", true);
                }
            });
        });

        //通用表单post提交
        form.on('submit(submit)',function(event, treeId, treeNode){
            // function zTreeOnClick(event, treeId, treeNode) {       //第二步
            var treeObj = $.fn.zTree.getZTreeObj("treeDemo"),
                nodes = treeObj.getCheckedNodes(true),
                v = "";
            var str = "";
            var lev = "";
            for (var i = 0; i < nodes.length; i++) {
                // v += nodes[i].name + ",";
                // console.log("节点id:" + nodes[i].id + "节点名称" + v); //获取选中节点的值
                if ( str != "" ) {
                    str += ",";
                }
                str += nodes[i].id;
            }
            var arr = [];
            var arr = str.split(',');

            $.ajax({
                url: '<?php echo url("item/doAssign"); ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    ids : $('input[name="ids"]').val(),
                    org_list : arr
                },
                beforeSend: function(){},
                success: function(data){
                    if ( data.status == 1 ) {
                        layer.msg(data.message, {
                            time: 1500
                        },function(){
                            parent.window.location.reload();
                            var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                            parent.layer.close(index);
                        });
                    } else if ( data.status == 0 ) {
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
            // }
        });
    });
</script>

</body>
</html>