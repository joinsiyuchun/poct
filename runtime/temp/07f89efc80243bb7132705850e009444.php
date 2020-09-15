<?php /*a:1:{s:74:"/Applications/MAMP/htdocs/operator/application/admin/view/index/index.html";i:1582293826;}*/ ?>
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
    <!--    <link rel="stylesheet" type="text/css" href="/static/css/admin/layout.css" />-->
    <!--    <link rel="stylesheet" type="text/css" href="/static/css/admin/admin.css" />-->
    <link rel="stylesheet" type="text/css" href="/static/layui/css/layui.css" />
    <link rel="stylesheet" type="text/css" href="/static/css/admin/layuimini.css" />
    <link rel="stylesheet" type="text/css" href="/static/css/font-awesome-4.7.0/css/font-awesome.min.css" />
    <link rel="stylesheet" type="text/css" href="/static/css/iconfont/iconfont.css" />
    
    
    <!--[if lt IE 9]>
    <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
    <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style id="layuimini-bg-color"></style>
    <style>
        .copyright {
            z-index: 999;
            background-color: #fff;
            color: #666;
        }
    </style>
</head>
<body class="layui-layout-body layuimini-all">
<div class="layui-layout layui-layout-admin">
    <div class="layui-header header">
        <div class="layui-logo">
            <a href="">
                <img src="/static/images/logo-icon.png" alt="">
                <h1>一键修</h1>
            </a>
        </div>
        <a>
            <div class="layuimini-tool"><i title="展开" class="fa fa-outdent" data-side-fold="1"></i></div>
        </a>

        <ul class="layui-nav layui-layout-left layui-header-menu layui-header-pc-menu mobile layui-hide-xs">
        </ul>
<!--        <ul class="layui-nav layui-layout-left layui-header-menu mobile layui-hide-sm">-->
<!--            <li class="layui-nav-item">-->
<!--                <a href="javascript:;"><i class="fa fa-list-ul"></i> 选择模块</a>-->
<!--                <dl class="layui-nav-child layui-header-mini-menu">-->
<!--                </dl>-->
<!--            </li>-->
<!--        </ul>-->

        <ul class="layui-nav layui-layout-right">
            <li class="layui-nav-item">
                <a href="javascript:;" data-refresh="刷新"><i class="fa fa-refresh"></i></a>
            </li>
            <li class="layui-nav-item">
                <a href="javascript:;" data-clear="清理" class="layuimini-clear"><i class="fa fa-trash-o"></i></a>
            </li>
            <li class="layui-nav-item layuimini-setting">
                <a href="javascript:;"><?php echo htmlentities(app('session')->get('admin_username')); ?></a>
                <dl class="layui-nav-child">
                    <dd>
                        <a href="javascript:;" onclick="editPassword();" data-title="修改密码" data-icon="fa fa-gears">修改密码</a>
                    </dd>
                    <dd>
                        <a href="javascript:;" class="login-out" onclick="logout()">退出登录</a>
                    </dd>
                </dl>
            </li>
            <li class="layui-nav-item layuimini-select-bgcolor mobile layui-hide-xs">
                <a href="javascript:;" data-bgcolor="配色方案"><i class="fa fa-ellipsis-v"></i></a>
            </li>
        </ul>
    </div>

    <div class="layui-side layui-bg-black">
        <div class="layui-side-scroll layui-left-menu">
            <ul class="layui-nav layui-nav-tree layui-left-nav-tree layui-this" id="currency">
                <?php if(!empty($menu)): if(is_array($menu) || $menu instanceof \think\Collection || $menu instanceof \think\Paginator): if( count($menu)==0 ) : echo "" ;else: foreach($menu as $key=>$vo): ?>
                <li class="layui-nav-item layui-nav-itemed">
                    <a href="javascript:;" class="layui-menu-tips">
                        <i class="<?php echo htmlentities($vo['icon']); ?>"></i>
                        <span class="layui-left-nav"> <?php echo htmlentities($vo['title']); ?></span>
                        <?php if(!empty($vo['children'])): ?>
                        <span class="layui-nav-more"></span>
                        <?php endif; ?>
                    </a>
                    <?php if(!empty($vo['children'])): ?>
                    <dl class="layui-nav-child">
                        <?php if(is_array($vo['children']) || $vo['children'] instanceof \think\Collection || $vo['children'] instanceof \think\Paginator): if( count($vo['children'])==0 ) : echo "" ;else: foreach($vo['children'] as $key=>$v): ?>
                        <dd>
                            <a href="javascript:;" class="layui-menu-tips" data-type="tabAdd" data-tab-mpi="m-p-i-1" data-tab="/<?php echo htmlentities($v['path']); ?>" target="_self">
                                <i class="<?php echo htmlentities($v['icon']); ?>"></i>
                                <span class="layui-left-nav"> <?php echo htmlentities($v['title']); ?></span>
                            </a>
                        </dd>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </dl>
                    <?php endif; ?>
                </li>
                <?php endforeach; endif; else: echo "" ;endif; ?>
                <?php endif; ?>
                <span class="layui-nav-bar" style="top: 101px; height: 0px; opacity: 0;"></span>
            </ul>
        </div>
    </div>

    <div class="layui-body">
        <div class="layui-tab" lay-filter="layuiminiTab" id="top_tabs_box">
            <ul class="layui-tab-title" id="top_tabs">
                <li class="layui-this" id="layuiminiHomeTabId" lay-id="page/welcome-1.html?mpi=m-p-i-0">
                    <i class="iconfont">&#xe87c;</i> <span>首页</span>
                </li>
            </ul>
            <ul class="layui-nav closeBox">
                <li class="layui-nav-item">
                    <a href="javascript:;"> <i class="fa fa-dot-circle-o"></i> 页面操作</a>
                    <dl class="layui-nav-child">
                        <dd><a href="javascript:;" data-page-close="other"><i class="fa fa-window-close"></i> 关闭其他</a></dd>
                        <dd><a href="javascript:;" data-page-close="all"><i class="fa fa-window-close-o"></i> 关闭全部</a></dd>
                    </dl>
                </li>
            </ul>
            <div class="layui-tab-content clildFrame">
                <div id="layuiminiHomeTabIframe" class="layui-tab-item layui-show">
                    <iframe width="100%" height="100%" frameborder="0" src="<?php echo url('index/home'); ?>"></iframe>
                </div>
            </div>
        </div>
    </div>

    <div class="layui-footer copyright" style="">
        Copyright &copy; 2020 <a href="https://www.zhangshaoping.com" target="_blank">一键修</a> 版权所有  
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
    
    // 修改密码
    function editPassword() {
        var index = layer.open({
            type: 2,
            title: '<i class=iconfont>&#xe7c7;</i> 修改密码',
            area: ['550px', '650px'],
            content: ['<?php echo url("index/editPassword"); ?>', 'no'],
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
    
    // 退出登录
    function logout() {
        layer.confirm('您确定要退出吗？',{
            icon: 3,
            skin: 'layer-ext-moon'
        },function(index){
            window.location = '<?php echo url("login/logout"); ?>';
        });
    }
    
</script>


</body>
</html>