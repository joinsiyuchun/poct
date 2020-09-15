<?php
namespace app\api\controller;

use app\common\controller\Api;
use app\api\common\model\Item as ItemModel;
use app\api\common\model\Workorder as WorkorderModel;

class Dashboard extends Api
{
    protected $checkLoginExclude = [];

    public function faultrate()
    {
        $org_id=$this->org["id"];
        $total = ItemModel::where(['is_backup'=>0,'status'=>1])->count();
        $fault=WorkorderModel::where(['org_id'=>$org_id])->where('status','in','3,4')->count();
        if($total==0){
            $rate=0;
        }else{
            $rate=round($fault/$total,2)*100;
        }
        $fault_rate["rate"]=$rate;
        return json($fault_rate);
    }

}
