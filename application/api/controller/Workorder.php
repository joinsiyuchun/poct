<?php
namespace app\api\controller;

use app\common\controller\Api;
use app\api\common\model\Catagory as CategoryModel;
use app\api\common\model\Workorder as OrderModel;
use app\api\common\model\Notification as NotificationModel;
use app\api\common\model\Item as ItemModel;

class Workorder extends Api
{
    protected $checkLoginExclude = [];

    public function itemlist()
    {
        $url = $this->request->domain() . '/static/uploads/';
        $department=$this->request->get('department/d', 0);
        $category = CategoryModel::field('id,name')->order('sort', 'asc')->select()->toArray();
        $items = ItemModel::where(['status'=>1])
            ->order('id', 'asc')
            ->select();
        $itemlist=[];
        $org_id=$this->org['id'];
        foreach($items as $k=>$v){
            $orglist=explode('|',$v["org_list"]);
            if(in_array($org_id,$orglist)){
                $itemlist[$k]["id"]=$v["id"];
                $itemlist[$k]["category_id"]=$v["catagoryid"];
                $itemlist[$k]["name"]=$v["catagory"]["name"].'-'.$v["code"];
                $itemlist[$k]["image_url"]= $url .$v["image_url"];
                $itemlist[$k]["sn"]=$v["sn"];
                $itemlist[$k]["pn"]=$v["pn"];
                $itemlist[$k]["pid"]=$v["pid"];
            }
        }
        $data = Array();
        foreach ($category as $v) {
            $data[$v['id']] = array_merge($v, ['item' => []]);
            foreach ($itemlist as $vv) {
                if ($v['id'] === $vv['category_id']) {
                    $data[$v['id']]['item'][$vv['id']] = $vv;
                }
            }
        }
        return json([
            'list' => $data
        ]);
    }

    public function query()
    {
        $id = $this->request->get('id/d', 0);
        $notification = NotificationModel::get($id);
        if (empty($notification)) {
            $this->error('订单不存在');
        }
        $url = $this->request->domain() . '/static/uploads/';
        $order["memo"]=$notification["memo"];
        $order["code"]=$notification["code"];
        $order["report_time"]=$notification["create_time"];
        $workorderlist = $notification->workorder;
        foreach ($workorderlist as $k => $w) {
            $order["location"]=$w["location"];
            $order["items"][$k]["image_url"] = $url . $w["items"]["image_url"];
            $order["items"][$k]["name"] = $w["items"]["catagory"]["name"];
            $order["items"][$k]["sn"] = $w["items"]["sn"];
            $order["items"][$k]["status"] = $w["status"];
            $order["items"][$k]["orderno"] = $w["id"];
            $order["items"][$k]["accept_time"] = $w["accept_time"];
            $order["items"][$k]["complete_time"] = $w["complete_time"];
            $order["items"][$k]["halt_time"] = $w["halt_time"];
            $order["items"][$k]["cost"] = $w["cost"];
            $order["items"][$k]["service_cost"] = $w["service_cost"];
            $order["items"][$k]["accessory_cost"] = $w["accessory_cost"];
        }
        return json($order);
    }

    public function querylist()
    {
        $status = $this->request->get('type/d', 0);
        $begindate = $this->request->get('begindate', '2010-01-01 00:00:00');

        $enddate = $this->request->get('enddate', '2050-01-01 00:00:00');
        $enddatetime=$enddate.'23:59:59';
        switch($status){
            case 0:
                $workorderlist = OrderModel::where("status",$status)->whereBetweenTime("report_time",$begindate,$enddatetime)->order("create_time","desc")->select();
                break;
            case 1:
                $workorderlist = OrderModel::where("status",$status)->whereBetweenTime("accept_time",$begindate,$enddatetime)->order("create_time","desc")->select();
                break;
            case 2:
                $workorderlist = OrderModel::where("status",$status)->whereBetweenTime("complete_time",$begindate,$enddatetime)->order("create_time","desc")->select();
                break;
            default:
                $workorderlist = OrderModel::where("status",$status)->whereBetweenTime("report_time",$begindate,$enddatetime)->order("create_time","desc")->select();
                break;
        }
         if (empty($workorderlist->toArray())) {
            return null;
        }
        $url = $this->request->domain() . '/static/uploads/';
        foreach ($workorderlist as $k => $w) {
            $order["items"][$k]["id"] = $w["id"];
            $order["items"][$k]["image_url"] = $url . $w["items"]["image_url"];
            $order["items"][$k]["name"] = $w["items"]["catagory"]["name"];//设备名称
            $order["items"][$k]["sn"] = $w["items"]["sn"];//sn
            $order["items"][$k]["pn"] = $w["items"]["pn"];//pn
            $order["items"][$k]["org"] = $w["notification"]["org"]["name"];//org
            $order["items"][$k]["dept"] = $w["notification"]["dept"]["name"];//org
            $order["items"][$k]["code"] = $w["notification"]["code"];//报修单号
            $order["items"][$k]["create_time"] = $w["create_time"];
            $order["items"][$k]["accept_time"] = $w["accept_time"];
            $order["items"][$k]["complete_time"] = $w["complete_time"];
            $order["items"][$k]["halt_time"] = $w["halt_time"];
            $order["items"][$k]["cost"] = $w["cost"];
            $order["items"][$k]["service_cost"] = $w["service_cost"];
            $order["items"][$k]["accessory_cost"] = $w["accessory_cost"];
        }
        return json($order);
    }
    public function confirmorder()
    {
        $id = $this->request->post('id/d', 0);
        $comment = $this->request->post('comment/s', '', 'trim');
        $location = $this->request->post('location/s', '', 'trim');
        $v["memo"] = $comment;
        $v["item_id"]=$id;
        $v["status"] = 0;
        $v["org_id"] = $this->org["id"];
        $v["creater_id"] = $this->user["id"];
        $notify = NotificationModel::create($v);
        $notification_id = $notify->id;
        $notification_code = $this->code($notification_id);
        NotificationModel::where("id", $notification_id)->update(["code" => $notification_code]);
        $workorder["notification_id"] = $notification_id;
        $workorder["item_id"] = $id;
        $workorder["org_id"] = $this->org["id"];
        $workorder["status"] = 0;
        $workorder["location"] = $location;
        OrderModel::create($workorder);
        return json($notification_id);
    }

    public function confirm()
    {
        $formdata = $this->request->post('formdata/a');
        $comment=$formdata["comment"];
        $location=$formdata["location"];
        $items=$formdata["item"];
        $v["memo"] = $comment;
        $v["item_id"]=$items;
        $v["status"] = 0;
        $v["org_id"] = $this->org["id"];
        $v["creater_id"] = $this->user["id"];
        $notify = NotificationModel::create($v);
        $notification_id = $notify->id;
        $notification_code = $this->code($notification_id);
        NotificationModel::where("id", $notification_id)->update(["code" => $notification_code]);
        foreach($items as $k=>$id){
            $workorder["notification_id"] = $notification_id;
            $workorder["item_id"] = $id;
            $workorder["org_id"] = $this->org["id"];
            $workorder["status"] = 0;
            $workorder["location"] = $location;
            $workorder["report_time"] = date('Y-m-d H:i:s');;
            OrderModel::create($workorder);
        }

        return json($notification_id);
    }

    public function cancel()
    {
        $id = $this->request->post('id/d');
        $workorder=ordermodel::get($id);
        $workorder->status=-1;
        $workorder->save();
        return json([
            'result' => "订单取消成功"
        ]);
    }
    public function accept()
    {
        $id = $this->request->post('id/d');
        $workorder=ordermodel::get($id);
        $workorder->status=1;
        $workorder->receptor_id=$this->user["id"];
        $workorder->accept_time=date("Y-m-d H:i:s");
        $workorder->save();
        return json([
            'result' => "订单取消成功"
        ]);
    }
    public function refix()
    {
        $id = $this->request->post('id/d');
        $workorder=ordermodel::get($id);
        $workorder->status=2;
        $workorder->receptor_id=$this->user["id"];
        $workorder->accept_time=date("Y-m-d H:i:s");
        $workorder->save();
        return json([
            'result' => "退回成功"
        ]);
    }
    public function complete()
    {
        $id = $this->request->post('id/d');
        $workorder=ordermodel::get($id);
        $workorder->status=2;
        $workorder->completed_by=$this->user["id"];
        $workorder->complete_time=date("Y-m-d H:i:s");
        $workorder->save();
        return json([
            'result' => "订单取消成功"
        ]);
    }

    public function createOrder()
    {
        $order = $this->request->post('order/a', []);
        $notification["org_id"] = $this->org["id"];
        $notification["creater_id"] = $this->user["id"];
        $notification["status"] = -1;
        $notify = NotificationModel::create($notification);
        $notification_id = $notify->id;
        $notification_code = $this->code($notification_id);
        NotificationModel::where("id", $notification_id)->update(["code" => $notification_code]);
        foreach ($order as $v) {
            $workorder["notification_id"] = $notification_id;
            $workorder["item_id"] = $v["id"];
            $workorder["org_id"] = $this->org["id"];
            $workorder["status"] = -1;
            OrderModel::create($workorder);
        }
        return json(['order_id' => $notification_id]);
    }

    protected function code($id)
    {
        return 'A' . str_pad($id, 2, '0', STR_PAD_LEFT);
    }

    public function orderlist()
    {
        $last_id = $this->request->get('last_id/d', 0);
        $status = $this->request->get('status/d', 0);
        $row = min(max($this->request->get('row/d', 1), 1), 99);
        $order = NotificationModel::where(['creater_id' => $this->user['id'], 'status' => $status]);
        if ($last_id) {
            $order->where('id', '<', $last_id);
        }
        $list = $order->order('id', 'desc')->limit($row)->select();
        $last_id = 0;
        if (!$list->isEmpty()) {
            $last_id = $list[count($list) - 1]['id'];
        }
        foreach ($list as $k => $v) {
            $item_id = OrderModel::where('notification_id', $v['id'])->limit(1)->value('item_id');
            $item=ItemModel::get($item_id);
            $list[$k]['first_item_name'] =$item["catagory"]["name"] ;
            $list[$k]['count']=OrderModel::where('notification_id', $v['id'])->count();
            $list[$k]['dept']=$v["org"]["name"];
        }
        return json(['list' => $list, 'last_id' => $last_id]);
    }
}
