<?php


namespace app\index\controller;


use app\admin\common\controller\Base;

use app\index\common\model\Item_cost as CostModel;

use think\facade\Request;



class Cost extends Base
{
    // 接修单管理首页
    public function index()
    {
        $this -> view -> assign('title', '成本接口数据管理');
        return $this -> view -> fetch('index');
    }

    // 接修单列表
    public function costList()
    {
        $map = [];
        $wolist= [];
        // 搜索功能
        $keywords = Request::param('keywords');
        if ( !empty($keywords) ) {
            $map[] = ['id', 'like', '%'.$keywords.'%'];
        }

        // 定义分页参数
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;

        // 获取接修单信息
        $CostList = CostModel::where($map)
            -> page($page, $limit)
            -> select();

        $total = count(CostModel::where($map)->select());
        $result = array("code" => 0, "msg" => "查询成功", "count" => $total, "data" => $CostList);
        return json($result);
    }



    // 删除接修单
    public function del()
    {
        $id = Request::param('id');

        // 执行删除操作
        try {
            CostModel::where('id', $id) -> delete();
        } catch (\Exception $e) {
            return resMsg(0, '成本删除失败' . '<br>' . $e->getMessage(), 'index' );
        }
        return resMsg(1, '成本删除成功', 'index');
    }


}