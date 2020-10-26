<?php


namespace app\index\controller;


use app\admin\common\controller\Base;

use app\index\common\model\Hisorder as HisorderModel;

use app\index\common\model\Pdalog as PdalogModel;

use think\facade\Request;



class Hisorder extends Base
{

    public function index()
    {
        $this -> view -> assign('title', '医嘱数据管理');
        return $this -> view -> fetch('index');
    }


    public function hisorderList()
    {
        $map = [];
        $wolist= [];
        // 搜索功能
        $keywords = Request::param('keywords');
        if ( !empty($keywords) ) {
            $map[] = ['diagnosis_no', 'like', '%'.$keywords.'%'];
        }

        // 定义分页参数
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;

        // 获取接修单信息
        $hisorderList = HisorderModel::where($map)
            -> page($page, $limit)
            -> select();
        foreach($hisorderList as $k=>$v){
            $wolist[$k]["id"]=$v["id"];
            $wolist[$k]["order_department"]=$v->org["name"];
            $wolist[$k]["diagnosis_catagory"]=$v["diagnosis_catagory"];
            $wolist[$k]["request_id"]=$v["request_id"];
            $wolist[$k]["payment_time"]=$v["payment_time"];
            $wolist[$k]["diagnosis_no"]=$v["diagnosis_no"];
            $wolist[$k]["diagnosis_id"]=$v["diagnosis_id"];
            $wolist[$k]["prescription_id"]=$v["prescription_id"];
            $wolist[$k]["pricelist_id"]=$v["pricelist_id"];
            $wolist[$k]["diaglist_id"]=$v["diaglist_id"];
            $wolist[$k]["item_code"]=$v["item_code"];
            $wolist[$k]["item_name"]=$v["item_name"];
            $wolist[$k]["item_uom"]=$v["item_uom"];
            $wolist[$k]["item_unitprice"]=$v["item_unitprice"];
            $wolist[$k]["item_quantity"]=$v["item_quantity"];
            $wolist[$k]["item_totalprice"]=$v["item_totalprice"];
        }
        $total = count(HisorderModel::where($map)->select());
        $result = array("code" => 0, "msg" => "查询成功", "count" => $total, "data" => $wolist);
        return json($result);
    }




    public function del()
    {
        $id = Request::param('id');

        // 执行删除操作
        try {
            HisorderModel::where('id', $id) -> delete();
        } catch (\Exception $e) {
            return resMsg(0, '检查单删除失败' . '<br>' . $e->getMessage(), 'index' );
        }
        return resMsg(1, '检查单删除成功', 'index');
    }

    // 编辑对应的PDA记录
    public function edit()
    {
        // 获取对应的HIS收费id
        $id = Request::param('id');
        $hisorder=HisorderModel::where('id', $id) -> find();
        $pdalog=[];
        $diagnosisId=$hisorder["diagnosis_id"];
        $prescriptioId=$hisorder["prescription_id"];
        $pdalog = PdalogModel::where(['diagnosis_id'=>$diagnosisId,'prescription_id'=>$prescriptioId]) -> find();

        // 设置模板变量
        $this -> view -> assign('title', '编辑检查设备信息');
        $this -> view -> assign('pdalog', $pdalog);

        // 渲染模板
        return $this -> view -> fetch('edit');
    }

    // 执行编辑PDA记录
    public function doEdit()
    {
        // 1. 获取的用户提交的信息
        $data = Request::param();

        // 执行编辑操作
        try {
            $pdalog = PdalogModel::where('id', $data['id']) -> find();
            if (!empty($pdalog)) {
                PdalogModel::update($data);
            }else{
                PdalogModel::create($data);
            }

        } catch (\Exception $e) {
            return resMsg(0, '编辑失败' . '<br>' . $e->getMessage(), 'edit' );
        }
        return resMsg(1, '编辑成功', 'index');
    }


}