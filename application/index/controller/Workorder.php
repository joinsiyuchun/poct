<?php


namespace app\index\controller;


use app\admin\common\controller\Base;
use app\index\common\model\Org as OrgModel;
use app\index\common\model\Item as ItemModel;
use app\index\common\model\Workorder as WorkorderModel;
use app\index\common\model\Settlement as SettlementModel;
use app\index\common\model\Notification as NotificationModel;
use think\facade\Request;
use think\facade\Session;
use think\facade\Tree;


class Workorder extends Base
{
    // 接修单管理首页
    public function index()
    {
        $this -> view -> assign('title', '接修单管理');
        return $this -> view -> fetch('index');
    }

    // 接修单列表
    public function workorderList()
    {
        $map = [];
        // 搜索功能
        $keywords = Request::param('keywords');
        if ( !empty($keywords) ) {
            $map[] = ['id', 'like', '%'.$keywords.'%'];
        }

        // 定义分页参数
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;

        // 获取接修单信息
        $workorderList = WorkorderModel::where($map)
            -> page($page, $limit)
            -> order('id', 'desc')
            -> select();
        foreach($workorderList as $k=>$v){
            $wolist[$k]["id"]=$v["id"];
            $wolist[$k]["code"]=$v->notification["code"];
            $wolist[$k]["status"]=$v["status"];
            $wolist[$k]["is_halt"]=$v["is_halt"];
            $wolist[$k]["halt_time"]=$v["halt_time"];
            $wolist[$k]["report_org"]=$v->notification["org"]["name"];
            $wolist[$k]["item"]=$v->items["catagory"]["name"];
            $wolist[$k]["code"]=$v->items["code"];
            $wolist[$k]["sn"]=$v->items["sn"];
            $wolist[$k]["reporter"]=$v->notification["user"]["user_name"];
            $wolist[$k]["create_time"]=$v->notification["create_time"];
            $wolist[$k]["recepter"]=$v->user["user_name"];
            $wolist[$k]["accept_time"]=$v["accept_time"];
            $wolist[$k]["complete_time"]=$v["complete_time"];
            $wolist[$k]["completer"]=$v->finaluser["user_name"];
        }
        $total = count(WorkorderModel::where($map)->select());
        $result = array("code" => 0, "msg" => "查询成功", "count" => $total, "data" => $wolist);
        return json($result);
    }

    // 添加接修单
    public function add(){
         $orglist = OrgModel::where(['status'=>1,'pid'=>0])
            -> order('id', 'desc')
            -> field('id, name')
            -> select();

        $this -> view -> assign(
            [
                'title' => '新建报修',
                'orglist' => $orglist
            ]
        );
        return $this -> view -> fetch('add');
    }

    // 二级联动-获取组织下的设备
    public function getItem(){
        $org_id = Request::param('org_id');
        $itemlist = ItemModel::where('status',1)
            -> order('id', 'desc')
            -> select();
        $k=0;
        foreach($itemlist as $v) {
            $orglist = explode('|', $v['org_list']);
            if(!in_array($org_id,$orglist)){
                continue;
            }
            $wolist[$k]["id"] = $v["id"];
            $wolist[$k]["name"] = $v["catagory"]["name"];
            $wolist[$k]["code"] = $v["code"];
            $wolist[$k]["sn"] = $v["sn"];
            $k++;
        }
//        $result = array("code" => 0, "msg" => "查询成功",  "data" => $wolist);
        return json($wolist);
    }

    // 执行接修单添加
    public function doAdd()
    {
        // 获取的用户提交的信息
        $data = Request::param();

        // 执行添加操作
        try {
                $notification["org_id"]=$data["org_id"];
                $notification["creater_id"]=Session::get('admin_id');
                $notification["memo"]=$data["memo"];
                $notify=NotificationModel::create($notification);
                $notification_id=$notify->id;
                $notification_code=$this->code($notification_id);
                NotificationModel::where("id",$notification_id)->update(["code"=>$notification_code]);
                $workorder["notification_id"]=$notification_id;
                $workorder["item_id"]=$data["item_id"];
                $workorder["org_id"]=$data["org_id"];
                $workorder["location"]=$data["location"];
                $workorder["status"]=0;
                $workorder["report_time"]=date('Y-m-d H:i:s');
                WorkorderModel::create($workorder);
        } catch (\Exception $e) {
            return resMsg(0, '接修单添加失败' . '<br>' . $e->getMessage(), 'add' );
        }
        return resMsg(1, '接修单添加成功', 'index');
    }

    protected function code($id)
    {
        return 'A' . str_pad($id, 2, '0', STR_PAD_LEFT);
    }


    // 编辑接修单页面
    public function edit()
    {
        // 获取接修单id
        $workorderId = Request::param('id');

        // 根据接修单id查询要更新的接修单信息
        $workorderInfo = WorkorderModel::where('id', $workorderId) -> find();
        $orglist = OrgModel::where(['status'=>1,'pid'=>0])
            -> order('id', 'desc')
            -> field('id, name')
            -> select();
        $this -> view -> assign('orglist' , $orglist);
        // 设置模板变量
        $this -> view -> assign('title', '编辑工单信息');
        $this -> view -> assign('workorderInfo', $workorderInfo);
        $this -> view -> assign('workorderid', $workorderId);
        // 渲染模板
        return $this -> view -> fetch('edit');
    }

    // 执行编辑接修单操作
    public function doEdit()
    {
        // 1. 获取的用户提交的信息
        $data = Request::param();

        // 执行编辑操作
        try {
            $notification["org_id"]=$data["org_id"];

//            $notification["creater_id"]=Session::get('admin_id');
            $notification["memo"]=$data["memo"];
            $notification_id=$data["notification_id"];
            NotificationModel::where("id",$notification_id)->update($notification);
            $workorder["item_id"]=$data["item_id"];
            $workorder["org_id"]=$data["org_id"];
            $workorder["location"]=$data["location"];
            $workorder["status"]=$data["status"];
            $workorder["cost"]=$data["cost"];
            $workorder["is_halt"]=$data["is_halt"];
            $workorder["halt_time"]=$data["halt_time"];
            $workorder_id=$data["id"];
            $wo=WorkorderModel::get($workorder_id);
            if($workorder["status"]==1 and $wo["status"]!=1){

                $workorder["accept_time"]= date('Y-m-d h:i:s', time());
                $workorder["receptor_id"]=Session::get('admin_id');
            }
            if($workorder["status"]==2 and $wo["status"]!=2){
                $workorder["status"]=2;
                $workorder["complete_time"]= date('Y-m-d h:i:s', time());
                $workorder["completed_by"]=Session::get('admin_id');
            }
            WorkorderModel::where("id",$workorder_id)->update($workorder);;
        } catch (\Exception $e) {
            return resMsg(0, '接修单编辑失败' . '<br>' . $e->getMessage(), 'edit' );
        }
        return resMsg(1, '接修单编辑成功', 'index');
    }

    // 删除接修单
    public function del()
    {
        $id = Request::param('id');

        // 执行删除操作
        try {
            WorkorderModel::where('id', $id) -> delete();
        } catch (\Exception $e) {
            return resMsg(0, '接修单删除失败' . '<br>' . $e->getMessage(), 'index' );
        }
        return resMsg(1, '接修单删除成功', 'index');
    }



    //批量分配组织
    public function checkout()
    {
        // 获取节点id
        $ids = Request::param('id');
        $orglist = OrgModel::where(['status'=>1,'pid'=>0])
            -> order('id', 'desc')
            -> field('id, name')
            -> select();
        // 设置模板变量
        $this -> view -> assign('title', '工单结算');
        $this -> view -> assign('ids', $ids);
        $this -> view -> assign('orglist', $orglist);
        // 渲染模板
        return $this -> view -> fetch('checkout');
    }

    // 执行编辑接修单操作
    public function doCheckout()
    {
        // 1. 获取的用户提交的信息
        $data = Request::param();

        // 执行编辑操作
        try {
            $checkoutorder["org_id"]=$data["org_id"];
            $checkoutorder["amount"]=$data["amount"];
            $checkoutorder["stauts"]=1;
            $checkoutorder["settled_by"]=Session::get('admin_id');
            $checkoutorder["memo"]=$data["memo"];
            $checkoutorder["workorderlist"]=$data["ids"];
            $settlement=SettlementModel::create($checkoutorder);
            $settle_id=$settlement->id;
            $ids=explode('|',$data["ids"]);
            foreach($ids as $v){
                WorkorderModel::where("id",$v)->update(["settle_id"=>$settle_id,"status"=>3]);;
            }

        } catch (\Exception $e) {
            return resMsg(0, '接修单编辑失败' . '<br>' . $e->getMessage(), 'edit' );
        }
        return resMsg(1, '接修单编辑成功', 'index');
    }
}