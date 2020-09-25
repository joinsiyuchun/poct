<?php


namespace app\index\controller;


use app\admin\common\controller\Base;

use app\index\common\model\DiagInfo as DiagInfoModel;

use think\facade\Request;


class Diaginfo extends Base
{
    // 接修单管理首页
    public function index()
    {
        $this->view->assign('title', '检查单管理');
        return $this->view->fetch('index');
    }

    // 接修单列表
    public function diaginfoList()
    {
        $map = [];
        $wolist = [];
        // 搜索功能
        $keywords = Request::param('keywords');
        $page = Request::param('page', 1);
        $limit = Request::param('limit', 10);
        if (!empty($keywords)) {
            $map[] = ['id', 'like', '%' . $keywords . '%'];
        }

//        // 定义分页参数
//        $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
//        $page = isset($_GET['page']) ? $_GET['page'] : 1;

        // 获取接修单信息
        $DiagInfoList = DiagInfoModel::where($map)
            ->page($page, $limit)
            ->select();
        foreach ($DiagInfoList as $k => $v) {
            $wolist[$k]["id"] = $v["id"];
            $wolist[$k]["request_id"] = $v["request_id"];
            $wolist[$k]["patient_source"] = $v["patient_source"];
            $wolist[$k]["item_name"] = $v["item_name"];
            $wolist[$k]["item_id"] = $v["item_id"];
            $wolist[$k]["diag_name"] = $v["diag_name"];
            $wolist[$k]["function"] = $v["function"];
            $wolist[$k]["part"] = $v["part"];
            $wolist[$k]["department"] = $v["department"];
            $wolist[$k]["is_positive"] = $v["is_positive"];
            $wolist[$k]["prescribe_date"] = $v["prescribe_date"];
            $wolist[$k]["inspection_date"] = $v["inspection_date"];
            $wolist[$k]["report_date"] = $v["report_date"];
            $wolist[$k]["profit"] = $v["profit"];
        }
        $total = DiagInfoModel::where($map)->count();
        $result = array("code" => 0, "msg" => "查询成功", "count" => $total, "data" => $wolist);
        return json($result);
    }


    // 删除接修单
    public function del()
    {
        $id = Request::param('id');

        // 执行删除操作
        try {
            DiagInfoModel::where('id', $id)->delete();
        } catch (\Exception $e) {
            return resMsg(0, '检查单删除失败' . '<br>' . $e->getMessage(), 'index');
        }
        return resMsg(1, '检查单删除成功', 'index');
    }


}