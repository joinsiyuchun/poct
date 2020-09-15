<?php
namespace app\api\controller;

use app\common\controller\Api;
use app\api\common\model\Catagory as CategoryModel;
use app\api\common\model\Workorder as OrderModel;
use app\api\common\model\Notification as NotificationModel;
use app\api\common\model\Item as ItemModel;
use app\api\common\model\Org as OrgModel;

class Catagory extends Api
{
    protected $checkLoginExclude = [];

    public function catagorylistbyorg()
    {
        $url = $this->request->domain() . '/static/uploads/';
        $org_id=$this->org['id'];
        $orglist=OrgModel::where(["pid"=>$org_id,"status"=>1])->whereOr(["id"=>$org_id])->order("sort","desc")->select();
        foreach($orglist as $k=>$org){
            $data[$k]["id"]=$org["id"];
            if($org["id"]==$org_id){
                $data[$k]["name"]="无归属科室";
            }else{
                $data[$k]["name"]=$org["name"];
            }
            foreach($org["catagory"] as $j=>$item){
                $data[$k]["catagorylist"][$j]["id"]=$item["id"];
                $data[$k]["catagorylist"][$j]["name"]=$item["name"];
                $data[$k]["catagorylist"][$j]["img_url"]=$url.$item["img_url"];
            }

        }
        return json([
            'list' => $data
        ]);
    }

}
