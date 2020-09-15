<?php


namespace app\index\controller;


use app\admin\common\controller\Base;
use rbac\Rbac;
use think\App;
use think\facade\Session;
use think\facade\Request;
use app\admin\common\model\Admin;
use app\index\common\model\Analysis as AnalysisModel;


class Bianalysis extends Base
{
    // 后台管理首页
    public function mri()
    {
//        // 实例化RBAC类
//        $rbac = Rbac::instance();
//
//        // 根据角色获取菜单
//        $menu = $rbac -> getAuthMenu(Session::get('admin_role_id'));
//
//        // 设置模板变量
        $this -> view -> assign('title', 'MRI更新分析');
//        $this -> view -> assign('menu', $menu);

        // 渲染模板
        return $this -> fetch('mri');
    }

    // 后台管理控制台
    public function ct()
    {
        // 设置模板变量
        $this -> view -> assign([
            'title' => 'CT更新分析'
        ]);

        // 渲染模板
        return $this -> view -> fetch('ct');

    }

    // 编辑节点
    public function benefit()
    {
        // 获取节点id
        $itemId = Request::param('id');

        return $this -> view -> fetch('benefit');
    }



    // 编辑节点
    public function cost()
    {
        // 获取节点id
        $itemId = Request::param('id');

        return $this -> view -> fetch('cost');
    }

    // 编辑节点
    public function usage()
    {
        // 获取节点id
        $itemId = Request::param('id');

        return $this -> view -> fetch('usage');
    }

    // 编辑节点
    public function downtime()
    {
        // 获取节点id
        $itemId = Request::param('id');

        return $this -> view -> fetch('downtime');
    }



}