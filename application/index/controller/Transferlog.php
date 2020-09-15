<?php


namespace app\index\controller;


use app\admin\common\controller\Base;
use think\facade\Request;
use app\index\common\model\Transferlog as TransferLogModel;
use think\facade\Session;

class Transferlog extends Base
{

    public function index()
    {
        $this -> view -> assign('title', '设备转移日志管理');
        return $this -> view -> fetch('index');
    }

    // 转移日志列表
    public function logList()
    {
        // 定义全局查询条件
        $map = []; // 将所有的查询条件封装到这个数组中

        // 搜索功能
        $keywords = Request::param('keywords');
        if ( !empty($keywords) ) {
            $map[] = ['operator', 'like', '%'.$keywords.'%'];
        }

        // 定义分页参数
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;


        $logList = TransferLogModel::where($map)
            -> order('id', 'desc')
            -> page($page, $limit)
            -> select();
        $trloglist=[];
        foreach($logList as $k=>$v){
            $trloglist[$k]["id"]=$v["id"];
            $trloglist[$k]["operator"]=$v["user"]["user_name"];
            $trloglist[$k]["status"]=$v["status"];
            $trloglist[$k]["transfer_time"]=$v["transfer_time"];
            $trloglist[$k]["item"]=$v["item"]["catagory"]["name"].'-'.$v["item"]["sn"];
            $trloglist[$k]["location"]=$v["location"];
            $trloglist[$k]["org_id"]=$v["org"]["name"];
            $trloglist[$k]["memo"]=$v["memo"];
            $trloglist[$k]["type"]=$v["type"];
        }
        $total = count(TransferLogModel::where($map)->select());
        $result = array("code" => 0, "msg" => "查询成功", "count" => $total, "data" => $trloglist);
        return json($result);
    }

    // 删除质控日志
    public function delete()
    {
        if ( Request::isAjax() ) {
            // 执行删除操作
            try {
                $id = Request::param('id');
                TransferLogModel::where('id', $id) -> delete();
            } catch (\Exception $e) {
                return resMsg(0, '日志删除失败' . '<br>' . $e->getMessage(), 'index' );
            }
            return resMsg(1, '日志删除成功', 'index');
        } else {
            return resMsg(-1, '请求类型错误', 'index');
        }
    }
}