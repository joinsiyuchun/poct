<?php


namespace app\index\controller;


use app\admin\common\controller\Base;
use app\index\common\model\Monthreport as MonthreportModel;
use app\index\common\model\Costreport as CostreportModel;
use app\index\common\model\DepartmentAnalysis as DepartmentAnalysisModel;
use app\index\common\model\Org as OrgModel;
use app\index\common\model\Item_cost as ItemCostModel;

use think\facade\Request;




class Bireport extends Base
{

    // 效益月报表
    public function index()
    {
        // 设置模板变量
        $this -> view -> assign([
            'title' => '设备效益月报表'
        ]);

        // 渲染模板
        return $this -> view -> fetch('index');

    }

    public function monthreport()
    {
        $map = [];
        // 搜索功能
        $keywords = Request::param('keywords');
        if ( !empty($keywords) ) {
            $map[] = ['item_id', 'like', '%'.$keywords.'%'];
        }

        // 定义分页参数
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;

        // 获取产品目录信息
        $itemList = MonthreportModel::where($map)
            -> page($page, $limit)
            -> order('date_time', 'desc')
            -> order('item_id', 'desc')
            -> select();
        $monthlist=[];
        foreach($itemList as $k=>$v){
            $monthlist[$k]["item_id"]=$v["item_id"];
            $monthlist[$k]["item_code"]=$v->item["code"];
            $monthlist[$k]["catagory"]=$v->item->catagory["name"];
            $monthlist[$k]["date_time"]=$v["date_time"];
            $monthlist[$k]["total_income"]=$v["total_income"];
            $monthlist[$k]["inspection_times"]=$v["inspection_times"];
            $monthlist[$k]["total_cost"]=$v["total_cost"];
        }
        $total = count(MonthreportModel::where($map)->select());
        $result = array("code" => 0, "msg" => "查询成功", "count" => $total, "data" => $monthlist);
        return json($result);

    }

    // 成本月报表
    public function costreport()
    {
        // 设置模板变量
        $this -> view -> assign([
            'title' => '设备成本月报表'
        ]);

        // 渲染模板
        return $this -> view -> fetch('costreport');

    }

    //  部门分析报表
    public function departmentreport()
    {
        // 设置模板变量
        $this -> view -> assign([
            'title' => '部门绩效分析报表'
        ]);

        // 渲染模板
        return $this -> view -> fetch('departmentreport');

    }

    public function departmentprofitreport()
    {
        $map = [];
        // 搜索功能
        $keywords = Request::param('keywords');
        if ( !empty($keywords) ) {
            $map[] = ['department', 'like', '%'.$keywords.'%'];
        }

        // 定义分页参数
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;

        // 获取产品目录信息
        $itemList = DepartmentAnalysisModel::where($map)
            -> page($page, $limit)
            -> order('department', 'desc')
            -> select();
//        $monthlist=[];
//        foreach($itemList as $k=>$v){
//            $monthlist[$k]["item_id"]=$v["item_id"];
//            $monthlist[$k]["item_code"]=$v->item["code"];
//            if(isset($v->item->catagory)){
//                $monthlist[$k]["catagory"]=$v->item->catagory["name"];
//            }else{
//                $monthlist[$k]["catagory"]='无效设备';
//            }
//
//            $monthlist[$k]["date_time"]=$v["date_time"];
//            $monthlist[$k]["cost_type"]=$v["cost_type"];
//            $monthlist[$k]["cost_item"]=$v["cost_item"];
//            $monthlist[$k]["cost"]=$v["cost"];
//        }
        $total = count(DepartmentAnalysisModel::where($map)->select());
        $result = array("code" => 0, "msg" => "查询成功", "count" => $total, "data" => $itemList);
        return json($result);

    }


    public function costmonthreport()
    {
        $map = [];
        // 搜索功能
        $keywords = Request::param('keywords');
        if ( !empty($keywords) ) {
            $map[] = ['item_id', 'like', '%'.$keywords.'%'];
        }

        // 定义分页参数
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;

        // 获取产品目录信息
        $itemList = CostreportModel::where($map)
            -> page($page, $limit)
            -> order('date_time', 'desc')
            -> order('item_id', 'desc')
            -> select();
        $monthlist=[];
        foreach($itemList as $k=>$v){
            $monthlist[$k]["item_id"]=$v["item_id"];
            $monthlist[$k]["item_code"]=$v->item["code"];
            if(isset($v->item->catagory)){
                $monthlist[$k]["catagory"]=$v->item->catagory["name"];
            }else{
                $monthlist[$k]["catagory"]='无效设备';
            }

            $monthlist[$k]["date_time"]=$v["date_time"];
            $monthlist[$k]["cost_type"]=$v["cost_type"];
            $monthlist[$k]["cost_item"]=$v["cost_item"];
            $monthlist[$k]["cost"]=$v["cost"];
        }
        $total = count(CostreportModel::where($map)->select());
        $result = array("code" => 0, "msg" => "查询成功", "count" => $total, "data" => $monthlist);
        return json($result);

    }

    // 补录成本
    public function addcostpage()
    {
        // 设置模板变量
        $this -> view -> assign([
            'title' => 'CT效率效益分析'
        ]);

        // 渲染模板
        return $this -> view -> fetch('addcostpage');

    }

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
            $addcostorder["is_fix"]=$data["is_fix"];
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


    public function singleanalysis()
    {
//        // 实例化RBAC类
//        $rbac = Rbac::instance();
//
//        // 根据角色获取菜单
//        $menu = $rbac -> getAuthMenu(Session::get('admin_role_id'));
//
//        // 设置模板变量
        $this -> view -> assign('title', '单台设备效率效益分析');
//        $this -> view -> assign('menu', $menu);

        // 渲染模板
        return $this -> fetch('singleanalysis');
    }

}