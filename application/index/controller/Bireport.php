<?php


namespace app\index\controller;


use app\admin\common\controller\Base;
use app\index\common\model\Analysis as AnalysisModel;
use rbac\Rbac;
use think\App;
use think\facade\Session;
use think\facade\Request;
use app\admin\common\model\Admin;


class Bireport extends Base
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
        $this -> view -> assign('title', 'MRI效率效益分析');
//        $this -> view -> assign('menu', $menu);

        // 渲染模板
        return $this -> fetch('mri');
    }

    // 后台管理控制台
    public function ct()
    {
        // 设置模板变量
        $this -> view -> assign([
            'title' => 'CT效率效益分析'
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
    public function get_benefit()
    {
        $map = [];
        $keywords = Request::param('keywords');
        if ( !empty($keywords) ) {
            $map[] = ['code', 'like', '%' . $keywords . '%'];
        }

        $items = AnalysisModel::where($map)
            -> order('date', 'asc')
            -> field('id,date,benefit')
            -> select();
//            -> toArray();
        foreach($items as $i=>$v){
            $itemList[$i]["id"]=$v["id"];
            $itemList[$i]["date"]=$v["date"];
            $itemList[$i]["benefit"]=$v["benefit"];
        }

        $total = count($itemList);
        $result = array("code" => 0, "count" => $total, "data" => $itemList);
        return json($result);
    }

    // 编辑节点
    public function get_cost()
    {
        $map = [];
        $keywords = Request::param('keywords');
        if ( !empty($keywords) ) {
            $map[] = ['code', 'like', '%' . $keywords . '%'];
        }

        $items = AnalysisModel::where($map)
            -> order('date', 'asc')
            -> field('id,date,cost')
            -> select();
//            -> toArray();
        foreach($items as $i=>$v){
            $itemList[$i]["id"]=$v["id"];
            $itemList[$i]["date"]=$v["date"];
            $itemList[$i]["cost"]=$v["cost"];
        }

        $total = count($itemList);
        $result = array("code" => 0, "count" => $total, "data" => $itemList);
        return json($result);
    }

    // 编辑节点
    public function get_usage()
    {
        $map = [];
        $keywords = Request::param('keywords');
        if ( !empty($keywords) ) {
            $map[] = ['code', 'like', '%' . $keywords . '%'];
        }

        $items = AnalysisModel::where($map)
            -> order('date', 'asc')
            -> field('id,date,usage')
            -> select();
//            -> toArray();
        foreach($items as $i=>$v){
            $itemList[$i]["id"]=$v["id"];
            $itemList[$i]["date"]=$v["date"];
            $itemList[$i]["usage"]=$v["usage"];
        }

        $total = count($itemList);
        $result = array("code" => 0, "count" => $total, "data" => $itemList);
        return json($result);
    }

    // 编辑节点
    public function get_downtime()
    {
        $map = [];
        $keywords = Request::param('keywords');
        if ( !empty($keywords) ) {
            $map[] = ['code', 'like', '%' . $keywords . '%'];
        }

        $items = AnalysisModel::where($map)
            -> order('date', 'asc')
            -> field('id,date,downtime')
            -> select();
//            -> toArray();
        foreach($items as $i=>$v){
            $itemList[$i]["id"]=$v["id"];
            $itemList[$i]["date"]=$v["date"];
            $itemList[$i]["downtime"]=$v["downtime"];
        }

        $total = count($itemList);
        $result = array("code" => 0, "count" => $total, "data" => $itemList);
        return json($result);
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