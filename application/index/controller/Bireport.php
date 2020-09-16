<?php


namespace app\index\controller;


use app\admin\common\controller\Base;
use app\index\common\model\Analysis as AnalysisModel;
use app\index\common\model\Org as OrgModel;
use app\index\common\model\Item_cost as ItemCostModel;

use think\facade\Request;




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
        // 获取id
        $itemId = Request::param('id');

        // 设置模板变量
        $this -> view -> assign('item_id', $itemId);
        return $this -> view -> fetch('benefit');
    }
// 编辑节点
    public function get_benefit()
    {
        $map = [];
        $itemList=[];
        $keywords = Request::param('keywords');
        $itemId = Request::param('id');
        if ( !empty($keywords) ) {
            $map[] = ['code', 'like', '%' . $keywords . '%'];
        }

        $items = AnalysisModel::where($map)
            ->where('equipid',$itemId)
            -> order('year', 'asc')
            -> order('month', 'asc')
            -> field('id,year,month,benefit')
            -> select();
//            -> toArray();
        foreach($items as $i=>$v){
            $itemList[$i]["id"]=$v["id"];
            $itemList[$i]["year"]=$v["year"];
            $itemList[$i]["month"]=$v["month"];
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
        $itemList=[];
        $keywords = Request::param('keywords');
        if ( !empty($keywords) ) {
            $map[] = ['code', 'like', '%' . $keywords . '%'];
        }

        $items = AnalysisModel::where($map)
            -> order('year', 'asc')
            -> order('month', 'asc')
            -> field('id,year,month,cost')
            -> select();
//            -> toArray();
        foreach($items as $i=>$v){
            $itemList[$i]["id"]=$v["id"];
            $itemList[$i]["year"]=$v["year"];
            $itemList[$i]["month"]=$v["month"];
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
        $itemList=[];
        $keywords = Request::param('keywords');
        if ( !empty($keywords) ) {
            $map[] = ['code', 'like', '%' . $keywords . '%'];
        }

        $items = AnalysisModel::where($map)
            -> order('year', 'asc')
            -> order('month', 'asc')
            -> field('id,year,month,usage')
            -> select();
//            -> toArray();
        foreach($items as $i=>$v){
            $itemList[$i]["id"]=$v["id"];
            $itemList[$i]["year"]=$v["year"];
            $itemList[$i]["month"]=$v["month"];
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
        $itemList=[];
        $keywords = Request::param('keywords');
        if ( !empty($keywords) ) {
            $map[] = ['code', 'like', '%' . $keywords . '%'];
        }

        $items = AnalysisModel::where($map)
            -> order('year', 'asc')
            -> order('month', 'asc')
            -> field('id,year,month,downtime')
            -> select();
//            -> toArray();
        foreach($items as $i=>$v){
            $itemList[$i]["id"]=$v["id"];
            $itemList[$i]["year"]=$v["year"];
            $itemList[$i]["month"]=$v["month"];
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

    //批量分配组织
    public function addcost()
    {
        // 获取节点id
        $ids = Request::param('id');
        $orglist = OrgModel::where(['status'=>1,'pid'=>0])
            -> order('id', 'desc')
            -> field('id, name')
            -> select();
        // 设置模板变量
        $this -> view -> assign('title', '补录成本');
        $this -> view -> assign('ids', $ids);
        $this -> view -> assign('orglist', $orglist);
        // 渲染模板
        return $this -> view -> fetch('addcost');
    }

    // 执行编辑接修单操作
    public function doAddcost()
    {
        // 1. 获取的用户提交的信息
        $data = Request::param();

        // 执行编辑操作
        try {

            $addcostorder["type"]=$data["type"];
            $addcostorder["start_date"]=$data["start_date"];
            $addcostorder["end_date"]=$data["end_date"];
            $addcostorder["amount"]=$data["amount"];
            $addcostorder["memo"]=$data["memo"];
            $ids=explode('|',$data["ids"]);
            foreach($ids as $v){
                $addcostorder["item_id"]=$v;
                ItemCostModel::create($addcostorder);
            }

        } catch (\Exception $e) {
            return resMsg(0, '成本补录失败' . '<br>' . $e->getMessage(), 'edit' );
        }
        return resMsg(1, '成本补录成功', 'index');
    }

}