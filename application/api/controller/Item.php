<?php


namespace app\api\controller;


use app\common\controller\Api;
use app\api\common\model\Transferlog as TransferlogModel;
use app\api\common\model\Qualitylog as QualitylogModel;
use app\api\common\model\Item as ItemModel;


class Item extends API
{
    public function itempage()
    {
        $id = $this->request->get('id/d', 0);
        $item=ItemModel::get($id);
        $url = $this->request->domain() . '/static/uploads/';
        $result["image_url"]=$url.$item["image_url"];
        $result["id"]=$item["id"];
        $result["code"]=$item["code"];
        $result["name"]=$item["catagory"]["name"];
        $result["code"]=$item["code"];
        $result["brand"]=$item["brand"];
        $result["model"]=$item["model"];
        $result["lbs"]["name"]=$item["location"];
        $result["lbs"]["latitude"]=$item["latitude"];
        $result["lbs"]["longitude"]=$item["longitude"];
        $childlist=ItemModel::where("pid",$id)->select();
        $itemlist=[];
        foreach($childlist as $i=>$v){
                $itemlist[$i]["name"]=$v["catagory"]["name"];
                $itemlist[$i]["id"]=$v["id"];
                $itemlist[$i]["code"]=$v["code"];
                $itemlist[$i]["sn"]=$v["sn"];
                $itemlist[$i]["pn"]=$v["pn"];
                $itemlist[$i]["img_url"]=$url.$v["image_url"];
        }
        return json([
            'itemdata' => $result,
            'list' =>$itemlist
        ]);
    }

    public function query()
    {
        $id = $this->request->get('id/d', 0);
        $item=ItemModel::get($id);
        $url = $this->request->domain() . '/static/uploads/';
        $result["image_url"]=$url.$item["image_url"];
        $result["id"]=$item["id"];
        $result["name"]=$item["catagory"]["name"];
        $result["sn"]=$item["sn"];
        $result["pn"]=$item["pn"];
        return json([
            'data' => $result
        ]);
    }

    public function listbycategory()
    {
        $category_id = $this->request->get('category_id/d', 0);
        $department_id = $this->request->get('department_id/d', 0);
        $itemlist=ItemModel::where(["status"=>1,"catagoryid"=>$category_id])->select();
        $url = $this->request->domain() . '/static/images/';
        $data=[];
        foreach($itemlist as $i=>$v){
           if(in_array($department_id,$v->orgs())){
               $data[$i]["type"]=$v["catagory"]["name"];
               $data[$i]["id"]=$v["id"];
               $data[$i]["code"]=$v["code"];
               $data[$i]["sn"]=$v["sn"];
               $data[$i]["pn"]=$v["pn"];
               $data[$i]["brand"]=$v["brand"];
               $data[$i]["model"]=$v["model"];
               $data[$i]["img_url"]=$url."logo-icon.png";
           }
        }
        return json([
            'data' => $data
        ]);
    }

    public function listbyitemid()
    {
        $item_id = $this->request->get('item_id/d', 0);
        $itemlist=ItemModel::where("id",$item_id)->select();
        $url = $this->request->domain() . '/static/images/';
        $data=[];
        foreach($itemlist as $i=>$v){
                $data[$i]["type"]=$v["catagory"]["name"];
                $data[$i]["id"]=$v["id"];
                $data[$i]["code"]=$v["code"];
                $data[$i]["sn"]=$v["sn"];
                $data[$i]["pn"]=$v["pn"];
                $data[$i]["brand"]=$v["brand"];
                $data[$i]["model"]=$v["model"];
                $data[$i]["img_url"]=$url."logo-icon.png";
        }
        return json([
            'data' => $data
        ]);
    }

    public function findbytype()
    {
        $id = $this->request->get('type/d', 0);
        $itemlist=ItemModel::where("status","1")->select();
        $data=[];
        $type="";
        $i=0;
        switch($id){
            case 1:
                $type='在用';
                foreach($itemlist as $k=>$v){
                    $wolist=$v["workorders"];
                    if(empty($wolist)){
                        $data[$i]["id"]=$v["id"];
                        $data[$i]["type"]=$v["catagory"]["name"];
                        $data[$i]["code"]=$v["code"];
                        $data[$i]["seriesno"]=$v["sn"];
                        $data[$i]["status"]=$type;
                        $i++;
                        continue;
                    }
                    foreach($wolist as $wo){
                        if(($wo["status"]==0 or $wo["status"]==1) and $wo["is_halt"]==1){
                            $data[$i]["id"]=$v["id"];
                            $data[$i]["type"]=$v["catagory"]["name"];
                            $data[$i]["code"]=$v["code"];
                            $data[$i]["seriesno"]=$v["sn"];
                            $data[$i]["status"]=$type;
                            $i++;
                            break;
                        }
                    }
                }
                break;
            case 2:
                $type='备机';
                foreach($itemlist as $k=>$v){
                    if($v["is_backup"]==1 ){
                        $data[$i]["id"]=$v["id"];
                        $data[$i]["type"]=$v["catagory"]["name"];
                        $data[$i]["code"]=$v["code"];
                        $data[$i]["seriesno"]=$v["sn"];
                        $data[$i]["status"]=$type;
                    }
                    $i++;
                }
                break;
            case 3: $type='报修待接修';
                $type='报修待接修';
                foreach($itemlist as $k=>$v){
                    $wolist=$v["workorders"];
                    foreach($wolist as $wo){
                        if($wo["status"]==0){
                            $data[$i]["id"]=$v["id"];
                            $data[$i]["type"]=$v["catagory"]["name"];
                            $data[$i]["code"]=$v["code"];
                            $data[$i]["seriesno"]=$v["sn"];
                            $data[$i]["status"]=$type;
                            $i++;
                            break;
                        }
                    }
                }
                break;
            case 4: $type='维修中';
                $type='报修待接修';
                foreach($itemlist as $k=>$v){
                    $wolist=$v["workorders"];
                    foreach($wolist as $wo){
                        if($wo["status"]==1){
                            $data[$i]["id"]=$v["id"];
                            $data[$i]["type"]=$v["catagory"]["name"];
                            $data[$i]["code"]=$v["code"];
                            $data[$i]["seriesno"]=$v["sn"];
                            $data[$i]["status"]=$type;
                            $i++;
                            break;
                        }
                    }
                }
                break;
        }
        return json([
            'list' => $data
        ]);
    }

    public function findbyparam()
    {
        $id = $this->request->get('type/d', 0);
        $sn = $this->request->get('sn/s', '');
        if(empty($sn)){
            $itemlist=ItemModel::where("status","1")->select();
        }else{
            $itemlist=ItemModel::where(["status"=>"1","sn"=>$sn])->select();
        }

        $data=[];
        $type="";
        $i=0;
        switch($id){
            case 1:
                $type='在用';
                foreach($itemlist as $k=>$v){
                    $wolist=$v["workorders"];
                    foreach($wolist as $wo){
                        if(($wo["status"]==0 or $wo["status"]==1) and $wo["is_halt"]==1){
                            $data[$i]["id"]=$v["id"];
                            $data[$i]["type"]=$v["catagory"]["name"];
                            $data[$i]["code"]=$v["code"];
                            $data[$i]["seriesno"]=$v["sn"];
                            $data[$i]["status"]=$type;
                            $i++;
                            break;
                        }
                    }
                }
                break;
            case 2:
                $type='备机';
                foreach($itemlist as $k=>$v){
                    if($v["is_backup"]==1 ){
                        $data[$i]["id"]=$v["id"];
                        $data[$i]["type"]=$v["catagory"]["name"];
                        $data[$i]["code"]=$v["code"];
                        $data[$i]["seriesno"]=$v["sn"];
                        $data[$i]["status"]=$type;
                    }
                    $i++;
                }
                break;
            case 3: $type='报修待接修';
                $type='报修待接修';
                foreach($itemlist as $k=>$v){
                    $wolist=$v["workorders"];
                    foreach($wolist as $wo){
                        if($wo["status"]==0){
                            $data[$i]["id"]=$v["id"];
                            $data[$i]["type"]=$v["catagory"]["name"];
                            $data[$i]["code"]=$v["code"];
                            $data[$i]["seriesno"]=$v["sn"];
                            $data[$i]["status"]=$type;
                            $i++;
                            break;
                        }
                    }
                }
                break;
            case 4: $type='维修中';
                $type='报修待接修';
                foreach($itemlist as $k=>$v){
                    $wolist=$v["workorders"];
                    foreach($wolist as $wo){
                        if($wo["status"]==1){
                            $data[$i]["id"]=$v["id"];
                            $data[$i]["type"]=$v["catagory"]["name"];
                            $data[$i]["code"]=$v["code"];
                            $data[$i]["seriesno"]=$v["sn"];
                            $data[$i]["status"]=$type;
                            $i++;
                            break;
                        }
                    }
                }
                break;
        }
        return json([
            'list' => $data
        ]);
    }

    public function backuplist()
    {

        $itemlist=ItemModel::where(["status"=>1,"is_backup"=>1,"pid"=>0])->select();
        $url = $this->request->domain() . '/static/uploads/';

        foreach($itemlist as $i=>$v){
            $data[$i]["id"]=$v["id"];
            $data[$i]["type"]=$v["catagory"]["name"];
            $data[$i]["code"]=$v["code"];
            $data[$i]["sn"]=$v["sn"];
            $data[$i]["pn"]=$v["pn"];
            $data[$i]["image_url"]=$url.$v["image_url"];
            $transfer_status = TransferlogModel::where(['item_id'=>$v["id"]])->order('transfer_time desc')->limit(1)->select()->toArray();
            if(empty($transfer_status)){
                $type="可外借";
                $location="";
                $transfer_time="";
            }elseif($transfer_status[0]["type"]==0){
                $type="已借出";
                $location=$transfer_status[0]["location"];
                $transfer_time=$transfer_status[0]["transfer_time"];
            }else{
                $type="可外借";
                $location=$transfer_status[0]["location"];
                $transfer_time=$transfer_status[0]["transfer_time"];
            }

            $data[$i]["status"]=$type;
            $data[$i]["location"]=$location;
            $data[$i]["transfer_time"]=$transfer_time;
        }

        return json([
            'list' => $data
        ]);
    }

    public function find()
    {
        $id = $this->request->get('id/d', 0);
        $currenttab = $this->request->get('type/d', 0);
        switch($currenttab){
            case 1:
                $type='每日检查';
                break;
            case 2:
                $type='每周检查';
                break;
            case 3:
                $type='预防性维护';
                break;
            default :
                $type='全部';
        }
        $data = ItemModel::get($id);
        $item["name"] = $data->catagory["name"];
        $item["id"] = $data["id"];
        $item["sn"] = $data["sn"];
        $item["pn"] = $data["pn"];
        $item["brand"] = $data["brand"];
        $item["model"] = $data["model"];
        $item["code"] = $data["code"];
        if($type=='全部'){
            $itemlist = QualitylogModel::where(['item_id'=>$id])->order('qc_time desc')->select();
        }else{
            $itemlist = QualitylogModel::where(['item_id'=>$id,'type'=>$currenttab])->order('qc_time desc')->select();
        }
        foreach($itemlist as $v=>$k){
            $itemlist[$v]["op_name"]=$k["user"]["user_name"];
        }
        return json([
            'item' => $item,
            'list'=>$itemlist
        ]);
    }

    public function findtransferlog()
    {
        $id = $this->request->get('id/d', 0);
        $currenttab = $this->request->get('type/d', 0);
        if($currenttab==2){
            $itemlist = TransferlogModel::where(['item_id'=>$id])->order('transfer_time desc')->select();
        }else {
            $itemlist = TransferlogModel::where(['item_id' => $id, 'type' => $currenttab])->order('transfer_time desc')->select();
        }
        $list=[];
        foreach($itemlist as $k=>$item){
            $list[$k]["operator_name"]=$item["operator"];
            $list[$k]["location"]=$item["location"];
            $list[$k]["transfer_time"]=$item["transfer_time"];
            $list[$k]["memo"]=$item["memo"];
            $list[$k]["type"]=$item["type"];
        }
        return json([
            'list'=>$list
        ]);
    }

    public function borrow() {
        $loaner = $this->request->post('loaner/s');
        $health = $this->request->post('health/d');
        $memo = $this->request->post('memo/s');
        $id = $this->request->post('id/d');
        $transferorder = [
            'item_id' => $id,
            'transfer_time' => date("Y-m-d h:i:s"),
            'type' => 0,
            'status' => 1,
            'memo' => $memo,
            'location' => $loaner,
            'health' => $health,
            'operator' =>  $this->user['realname'],
            'org_id' =>  $this->org["id"],
        ];
        TransferlogModel::create($transferorder);
        return json([
            'item' => "外借成功"
        ]);
    }

    public function itemreturn() {
        $loaner = $this->request->post('loaner/s');
        $health = $this->request->post('health/d');
        $memo = $this->request->post('memo/s');
        $id = $this->request->post('id/d');
        $transferorder = [
            'item_id' => $id,
            'transfer_time' => date("Y-m-d h:i:s"),
            'type' => 1,
            'status' => 1,
            'memo' => $memo,
            'location' => $loaner,
            'health' => $health,
            'operator' =>  $this->user['realname'],
            'org_id' =>  $this->org["id"],
        ];
        TransferlogModel::create($transferorder);
        return json([
            'item' => "归还成功"
        ]);
    }

    public function updatelocation() {
        $location = $this->request->get('location/s');
        $latitude = $this->request->get('latitude/f');
        $longitude = $this->request->get('longitude/f');
        $id = $this->request->get('id/d');
        $item = [
            'id' => $id,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'location' => $location
        ];
        ItemModel::update($item);
        return json([
            'msg' => "success"
        ]);
    }
}