<?php
namespace app\api\controller;

use app\common\controller\Api;
use app\api\common\model\Group as GroupModel;
use app\api\common\model\Org as OrgModel;
use think\Db;

class Catagory extends Api
{
    protected $checkLoginExclude = [];

//    public function catagorylistbyorg()
//    {
//        $url = $this->request->domain() . '/static/uploads/';
//        $org_id=$this->org['id'];
//        $orglist=OrgModel::where(["pid"=>$org_id,"status"=>1])->whereOr(["id"=>$org_id])->order("sort","desc")->select();
//        $data1=[];
//        foreach($orglist as $k=>$org){
//            $data1[$k]["id"]=$org["id"];
//            if($org["id"]==$org_id){
//                $data1[$k]["name"]="无归属科室";
//            }else{
//                $data1[$k]["name"]=$org["name"];
//            }
//            foreach($org["catagory"] as $j=>$item){
//                $data1[$k]["catagorylist"][$j]["id"]=$item["id"];
//                $data1[$k]["catagorylist"][$j]["name"]=$item["name"];
//                $data1[$k]["catagorylist"][$j]["img_url"]=$url.$item["img_url"];
//            }
//
//        }
//        return json([
//            'list' => $data1
//        ]);
//    }


    public function catagorylistbyorg()
    {
        $sql = <<<SQL
        SELECT
            b.id, b.name, a.org_list AS orgid
        FROM
            think_item a,
            think_catagory b
        WHERE
            a.catagoryid = b.id
        group by b.id, b.name, a.org_list
SQL;

        $catagorybyorg = Db::query($sql);
        $org_id=$this->org['id'];
        $orglist=GroupModel::get($org_id);
        $orgidlist = explode(',', $orglist['rules']);
        $response = [];
        $url = $this->request->domain() . '/static/images/';
        foreach($orgidlist as $k=>$org){

            foreach($catagorybyorg as $j=>$item){
                if($org==$item["orgid"]){
                    $response[$k]["catagorylist"][$j]["id"]=$item["id"];
                    $response[$k]["catagorylist"][$j]["name"]=$item["name"];
                    $response[$k]["catagorylist"][$j]["img_url"]=$url."logo-icon.png";
                }
            }
            if(!empty($response[$k]["catagorylist"])){
                $response[$k]["id"]=$org;
                $orgdata=OrgModel::get($org);
                $response[$k]["name"]=$orgdata['name'];
            }

        }

        return json([
            'list' => $response
        ]);
    }

}
