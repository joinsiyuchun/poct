<?php


namespace app\index\controller;


use app\admin\common\controller\Base;

use app\index\common\model\Pricelist as PricelistModel;

use think\facade\Request;



class Pricelist extends Base
{

    public function index()
    {
        $this -> view -> assign('title', '医保收费管理');
        return $this -> view -> fetch('index');
    }


    public function pricelist()
    {
        $map = [];
        $wolist= [];
        // 搜索功能
        $keywords = Request::param('keywords');
        if ( !empty($keywords) ) {
            $map[] = ['insurance_code', 'like', '%'.$keywords.'%'];
        }

        // 定义分页参数
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;

        // 获取接修单信息
        $hisorderList = PricelistModel::where($map)
            -> page($page, $limit)
            -> select();
        foreach($hisorderList as $k=>$v){
            $wolist[$k]["id"]=$v["id"];
            $wolist[$k]["insurance_code"]=$v["insurance_code"];
            $wolist[$k]["status"]=$v["status"];
            $wolist[$k]["start_date"]=$v["start_date"];
            $wolist[$k]["expire_date"]=$v["expire_date"];
            $wolist[$k]["price_code"]=$v["price_code"];
            $wolist[$k]["payment_method"]=$v["payment_method"];
            $wolist[$k]["item_name"]=$v["item_name"];
            $wolist[$k]["item_desc"]=$v["item_desc"];
            $wolist[$k]["exception"]=$v["exception"];
            $wolist[$k]["pcs"]=$v["pcs"];
            $wolist[$k]["unit_price"]=$v["unit_price"];
            $wolist[$k]["memo"]=$v["memo"];
            $wolist[$k]["coverage"]=$v["coverage"];
            $wolist[$k]["catagory"]=$v["catagory"];
        }
        $total = count(PricelistModel::where($map)->select());
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


}