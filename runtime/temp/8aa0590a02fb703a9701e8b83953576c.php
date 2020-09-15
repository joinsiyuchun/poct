<?php /*a:1:{s:73:"/Applications/MAMP/htdocs/operator/application/admin/view/index/home.html";i:1581839295;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>主页一</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    
    <link rel="stylesheet" type="text/css" href="/static/layui/css/layui.css" />
    <link rel="stylesheet" type="text/css" href="/static/css/public/public.css" />
    <link rel="stylesheet" type="text/css" href="/static/css/iconfont/iconfont.css" />
</head>
<style>
    .layui-top-box {padding:20px;color:#fff}
    .panel {margin-bottom:17px;background-color:#eee;border:1px solid transparent;border-radius: 5px;-webkit-box-shadow:0 1px 1px rgba(0,0,0,.05);box-shadow:0 1px 1px rgba(0,0,0,.05)}
    .panel-body {height: 75px;}
    .panel-icon {float: left; height: 75px; line-height: 75px; border-bottom-left-radius: 5px; border-top-left-radius: 5px; text-align: center}
    .panel-icon i {font-size: 45px; }
    .panel-info {float: left; padding: 10px; text-align: right;}
    .panel-info span {display: block;}
    .panel-info .counts {font-size: 30px; color: #333; line-height: 40px; text-shadow: 2px 1px 1px #999;}
    .panel-info .counts-name {font-size: 15px; color: #999; line-height: 20px;}
</style>
<body>
<div class="layuimini-container">
    <div class="layuimini-main layui-top-box">
        <div class="layui-row layui-col-space15">
            <div class="layui-col-md3">
                <div class="col-xs-6 col-md-3">
                    <div class="panel">
                        <div class="panel-body">
                            <div class="panel-icon layui-col-xs3 layui-col-md3 layui-bg-cyan">
                                <i class="iconfont">&#xe936;</i>
                            </div>
                            <div class="panel-info layui-col-xs9 layui-col-md9">
                                <span class="counts">100</span>
                                <span class="counts-name">管理员数量</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-col-md3">
                <div class="col-xs-6 col-md-3">
                    <div class="panel">
                        <div class="panel-body">
                            <div class="panel-icon layui-col-xs3 layui-col-md3 layui-bg-red">
                                <i class="iconfont">&#xea62;</i>
                            </div>
                            <div class="panel-info layui-col-xs9 layui-col-md9">
                                <span class="counts">100</span>
                                <span class="counts-name">角色数量</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-col-md3">
                <div class="col-xs-6 col-md-3">
                    <div class="panel">
                        <div class="panel-body">
                            <div class="panel-icon layui-col-xs3 layui-col-md3 layui-bg-orange">
                                <i class="iconfont">&#xe7f0;</i>
                            </div>
                            <div class="panel-info layui-col-xs9 layui-col-md9">
                                <span class="counts">100</span>
                                <span class="counts-name">权限节点数量</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="layui-col-md3">
                <div class="col-xs-6 col-md-3">
                    <div class="panel">
                        <div class="panel-body">
                            <div class="panel-icon layui-col-xs3 layui-col-md3 layui-bg-green">
                                <i class="iconfont">&#xe7fe;</i>
                            </div>
                            <div class="panel-info layui-col-xs9 layui-col-md9">
                                <span class="counts">100</span>
                                <span class="counts-name">登录次数</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="layui-top-box">
        <div class="layui-row layui-col-space15">
            <table class="layui-table">
                <thead>
                    <tr>
                        <th colspan="2" style="font-weight: bold;">系统信息</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>系统名称</th>
                        <td>ThinkPHP 5.1 RBAC权限管理系统</td>
                    </tr>
                    <tr>
                        <th>下载地址</th>
                        <td>
                            <a href="" class="layui-btn layui-btn-xs"><i class="iconfont">&#xf1b4;</i> GitHub</a>
                            <a href="" class="layui-btn layui-btn-xs"><i class="iconfont">&#xe9a1;</i> 码云</a></td>
                    </tr>
                    <tr>
                        <th>服务器IP</th>
                        <td><?php echo getHostByName(request()->host()); ?></td>
                    </tr>
                    <tr>
                        <th>操作系统</th>
                        <td><?php echo PHP_OS; ?></td>
                    </tr>
                    <tr>
                        <th>运行环境</th>
                        <td><?php echo $_SERVER["SERVER_SOFTWARE"]; ?></td>
                    </tr>
                    <tr>
                        <th>PHP版本</th>
                        <td><?php echo PHP_VERSION; ?></td>
                    </tr>
                    <tr>
                        <th>PHP运行方式</th>
                        <td><?php echo php_sapi_name(); ?></td>
                    </tr>
                    <tr>
                        <th>MySQL版本</th>
                        <td><?php echo getMysqlVersion(); ?></td>
                    </tr>
                    <tr>
                        <th>ThinkPHP</th>
                        <td><?php echo htmlentities($tp_version); ?></td>
                    </tr>
                    <tr>
                        <th>上传附件限制</th>
                        <td><?php echo ini_get("file_uploads"); ?>M</td>
                    </tr>
                    <tr>
                        <th>执行时间限制</th>
                        <td><?php echo ini_get("max_execution_time"); ?>s</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript" src="/static/js/jquery-3.4.1.min.js"></script>
<script type="text/javascript" src="/static/layui/layui.js"></script>
</body>
</html>