<?php


namespace app\index\controller;


use app\admin\common\controller\Base;

use app\index\common\model\Sample as SampleModel;

use app\index\common\model\Item as ItemModel;

use app\index\common\model\Patient as PatientModel;

use app\index\common\model\Request as RequestModel;

use think\facade\Request;

use think\Db;

use app\index\common\model\Pricelist as PricelistModel;



class Critical extends Base
{

    public function index()
    {
        $this -> view -> assign('title', '危急值管理');
        return $this -> view -> fetch('index');
    }


    public function sampleList()
    {
        $map = [];
        $wolist= [];
        // 搜索功能
        $keywords = Request::param('keywords');
        if ( !empty($keywords) ) {
            $map[] = ['code', 'like', '%'.$keywords.'%'];
        }

        // 定义分页参数
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 25;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;

        // 获取接修单信息
        $sampleList = SampleModel::where($map)
            -> where('status','0')
            -> page($page, $limit)
            -> select();
        foreach($sampleList as $k=>$v){
            $wolist[$k]["id"]=$v["id"];
            $wolist[$k]["org"]=$v["org"]["name"];
            $wolist[$k]["orderno"]=$v["request"]["operation_code"];
            $wolist[$k]["prescription_id"]=$v["prescription_id"];
            $wolist[$k]["patient_name"]=$v["patient"]["name"];
            $wolist[$k]["item_name"]=$v["item"]["catagory"]["name"];
            $wolist[$k]["code"]=$v["code"];
            $wolist[$k]["status"]=$v["status"];
            $wolist[$k]["type"]=$v["type"];
            $wolist[$k]["reason"]=$v["reason"];
            $wolist[$k]["create_time"]=$v["create_time"];
        }
        $total = count(SampleModel::where($map)->select());
        $result = array("code" => 0, "msg" => "查询成功", "count" => $total, "data" => $wolist);
        return json($result);
    }






    // 查看对应的设备信息
    public function equipment()
    {
        // 获取对应的设备数据
        $item=[];
        $id = Request::param('id');
        $item=ItemModel::where('id', $id) -> find();
        $this -> view -> assign('title', '检验设备信息');
        $this -> view -> assign('equipment',  $item);

        // 渲染模板
        return $this -> view -> fetch('equipment');
    }

    // 查看对应的病患信息
    public function patient()
    {
        // 获取对应的设备数据
        $patient=[];
        $id = Request::param('id');
        $patient=PatientModel::where('id', $id) -> find();
        if($patient["sexy"]==1){
            $patient["sexy"]="男";
        }else{
        $patient["sexy"]="女";
        }
        $this -> view -> assign('title', '病患信息');
        $this -> view -> assign('patient',  $patient);

        // 渲染模板
        return $this -> view -> fetch('patient');
    }

    // 查看对应的申请单信息
    public function itemrequest()
    {
        // 获取对应的申请单数据
        $request=[];
        $id = Request::param('id');
        $request=RequestModel::where('id', $id) -> find();

        $this -> view -> assign('title', '申请单信息');
        $this -> view -> assign('request',  $request);

        // 渲染模板
        return $this -> view -> fetch('request');
    }

    // 编辑节点
    public function edit()
    {
        // 获取节点id
        $sampleId = Request::param('id');


        $this -> view -> assign('catagoryId', $sampleId);

        // 设置模板变量
        $this -> view -> assign('title', '编辑设备');


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
            $sample = SampleModel::where('id', $data['id']) -> find();
            if (!empty($sample)) {
                $sample::update($data);
            }else{
                $sample::create($data);
            }

        } catch (\Exception $e) {
            return resMsg(0, '编辑失败' . '<br>' . $e->getMessage(), 'edit' );
        }
        return resMsg(1, '编辑成功', 'index');
    }

    // 医保收费列表
    public function pricelist()
    {
        // 定义分页参数
        $limit = Request::param('limit', 10);
        $page = Request::param('page', 1);
        $skip = $page <= 1 ? 0 : ($page-1) * $limit;
        $categoryId = Request::param('catagoryId', 0);

        // 搜索功能
        $keywords = Request::param('keywords');
        $query = Db::table(PricelistModel::TABLE_NAME);
        if ( !empty($keywords) ) {
            $query->where('item_name', 'like', '%'.$keywords.'%')
                ->whereOr('insurance_code', 'like', '%'.$keywords.'%');
            $sql = sprintf("select distinct p.*, (case when c.category_id =%s then true else false end) LAY_CHECKED from think_pricelist p left join (select * from think_catagory_pricelist where category_id =%s) c on c.pricelist_id = p.id where item_name like '%%%s%%' or insurance_code like '%%%s%%' order by LAY_CHECKED desc,p.id desc limit %s,%s",$categoryId,$categoryId, $keywords, $keywords, $skip,  $limit);
        }else{
            $sql = sprintf("select distinct p.*, (case when c.category_id =%s then true else false end) LAY_CHECKED from think_pricelist p left join (select * from think_catagory_pricelist where category_id =%s) c on c.pricelist_id = p.id order by LAY_CHECKED desc,p.id desc limit %s,%s",$categoryId,$categoryId, $skip,  $limit);
        }

        $priceList = Db::query($sql);
        $total = $query->count('id');
        $result = array("code" => 0, "msg" => "查询成功", "count" => $total, "data" => $priceList);
        return json($result);
    }


}