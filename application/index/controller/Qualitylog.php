<?php


namespace app\index\controller;


use app\admin\common\controller\Base;
use think\facade\Request;
use app\index\common\model\Qualitylog as QualityLogModel;
use think\facade\Session;

class Qualitylog extends Base
{
    // 登录日志首页
    // 管理员管理首页
    public function index()
    {
        $this -> view -> assign('title', '质控日志管理');
        return $this -> view -> fetch('index');
    }

    // 质控日志列表
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


        $logList = QualityLogModel::where($map)
            -> order('id', 'desc')
            -> page($page, $limit)
            -> select();

        foreach($logList as $k=>$v){
            $qcloglist[$k]["id"]=$v["id"];
            $qcloglist[$k]["operator"]=$v["user"]["user_name"];
            $qcloglist[$k]["qc_status"]=$v["qc_status"];
            $qcloglist[$k]["qc_time"]=$v["qc_time"];
            $qcloglist[$k]["item"]=$v["item"]["catagory"]["name"].'-'.$v["item"]["sn"];
            $qcloglist[$k]["location"]=$v["location"];
            $qcloglist[$k]["org_id"]=$v["org"]["name"];
            $qcloglist[$k]["memo"]=$v["memo"];
            $qcloglist[$k]["type"]=$v["type"];
        }
        $total = count(QualityLogModel::where($map)->select());
        $result = array("code" => 0, "msg" => "查询成功", "count" => $total, "data" => $qcloglist);
        return json($result);
    }

    // 删除质控日志
    public function delete()
    {
        if ( Request::isAjax() ) {
            // 执行删除操作
            try {
                $id = Request::param('id');
                QualityLogModel::where('id', $id) -> delete();
            } catch (\Exception $e) {
                return resMsg(0, '日志删除失败' . '<br>' . $e->getMessage(), 'index' );
            }
            return resMsg(1, '日志删除成功', 'index');
        } else {
            return resMsg(-1, '请求类型错误', 'index');
        }
    }
}