<?php
namespace app\api\controller;

use app\common\controller\Api;
use app\api\common\model\Org as OrgModel;

class Catagory extends Api
{
    protected $checkLoginExclude = [];

    public function catagorylistbyorg()
    {
        $url = $this->request->domain() . '/static/uploads/';
        $org_id=$this->org['id'];
        $orglist=OrgModel::where(["pid"=>$org_id,"status"=>1])->whereOr(["id"=>$org_id])->order("sort","desc")->select();
        $data1=[];
        foreach($orglist as $k=>$org){
            $data1[$k]["id"]=$org["id"];
            if($org["id"]==$org_id){
                $data1[$k]["name"]="无归属科室";
            }else{
                $data1[$k]["name"]=$org["name"];
            }
            foreach($org["catagory"] as $j=>$item){
                $data1[$k]["catagorylist"][$j]["id"]=$item["id"];
                $data1[$k]["catagorylist"][$j]["name"]=$item["name"];
                $data1[$k]["catagorylist"][$j]["img_url"]=$url.$item["img_url"];
            }

        }
        return json([
            'list' => $data1
        ]);
    }

}
