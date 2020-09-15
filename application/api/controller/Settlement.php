<?php
namespace app\api\controller;

use app\common\controller\Api;
use app\api\common\model\Catagory as CategoryModel;
use app\api\common\model\Workorder as OrderModel;
use app\api\common\model\Notification as NotificationModel;
use app\api\common\model\Item as ItemModel;
use app\api\common\model\Settlement as SettlementModel;

class Settlement extends Api
{
    protected $checkLoginExclude = [];

    public function settle()
    {
        $order_id = $this->request->post('id/d', 0);
        $settle_type = $this->request->post('settletype/s', 0);
        $total = $this->request->post('total/f', 0.0);
        $service = $this->request->post('service/f', 0.0);
        $accessary = $this->request->post('accessary/f', 0.0);
        $settlement["org_id"] = $this->org["id"];
        $settlement["settled_by"] = $this->user["id"];
        $settlement["settle_type"] = $settle_type;
        $settlement["status"] = 1;
        $settlement["amount"] = $total;
        $settlement["workorderlist"] = $order_id;
        SettlementModel::create($settlement);

        $workorder=OrderModel::get($order_id);
        $workorder["service_cost"] = $service;
        $workorder["accessory_cost"] = $accessary;
        $workorder["status"] = 4;
        $workorder->save();
        return json(['order_id' => $order_id]);
    }

}
