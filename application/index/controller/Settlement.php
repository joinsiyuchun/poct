<?php


namespace app\index\controller;


use app\admin\common\controller\Base;
use app\index\common\model\Workorder as WorkorderModel;
use app\index\common\model\Org as OrgModel;
use app\index\common\model\Settlement as SettlementModel;
use think\facade\Request;
use think\facade\Tree;


class Settlement extends Base
{
    // 结算管理首页
    public function index()
    {
        $this -> view -> assign('title', '结算管理');
        return $this -> view -> fetch('index');
    }

    // 结算列表
    public function settlementList()
    {
        $map = [];
        // 搜索功能
        $keywords = Request::param('keywords');
        if ( !empty($keywords) ) {
            $map[] = ['workorderlist', 'like', '%'.$keywords.'%'];
        }

        // 定义分页参数
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;

        // 获取结算信息
        $settlementList = SettlementModel::where($map)
            -> page($page, $limit)
            -> order('id', 'desc')
            -> select();
        foreach($settlementList as $k=>$v){
            $settlementList[$k]["org_name"]=$v["org"]["name"];

        }
        $total = count(SettlementModel::where($map)->select());
        $result = array("code" => 0, "msg" => "查询成功", "count" => $total, "data" => $settlementList);
        return json($result);
    }

    // 添加结算
    public function add()
    {
        $orglist = OrgModel::where(['status'=>1,'pid'=>0])
            -> order('id', 'desc')
            -> field('id, name')
            -> select();

        $this -> view -> assign(
            [
                'title' => '新建结算',
                'orglist' => $orglist
            ]
        );
        return $this -> view -> fetch('add');
    }

    // 执行结算添加
    public function doAdd()
    {
        // 获取的用户提交的信息
        $data = Request::param();
        // 执行添加操作
        try {
            SettlementModel::create($data);
        } catch (\Exception $e) {
            return resMsg(0, '结算添加失败' . '<br>' . $e->getMessage(), 'add' );
        }
        return resMsg(1, '结算添加成功', 'index');
    }

    // 编辑结算页面
    public function edit()
    {
        // 获取结算id
        $settlementId = Request::param('id');

        // 根据结算id查询要更新的结算信息
        $settlementInfo = SettlementModel::where('id', $settlementId) -> find();

        // 设置模板变量
        $this -> view -> assign('title', '编辑结算信息');
        $this -> view -> assign('settlementInfo', $settlementInfo);

        // 渲染模板
        return $this -> view -> fetch('edit');
    }

    // 执行编辑结算操作
    public function doEdit()
    {
        // 1. 获取的用户提交的信息
        $data = Request::param();

        // 执行编辑操作
        try {
            SettlementModel::update($data);
        } catch (\Exception $e) {
            return resMsg(0, '结算编辑失败' . '<br>' . $e->getMessage(), 'edit' );
        }
        return resMsg(1, '结算编辑成功', 'index');
    }

    // 删除结算
    public function del()
    {
        $id = Request::param('id');

        // 执行删除操作
        try {
            SettlementModel::where('id', $id) -> delete();
        } catch (\Exception $e) {
            return resMsg(0, '结算删除失败' . '<br>' . $e->getMessage(), 'index' );
        }
        return resMsg(1, '结算删除成功', 'index');
    }


    // 变更状态
    public function setStatus()
    {
        // 1. 获取用户提交的数据
        $data = Request::param();

        // 2. 取出数据
        $id = $data['id'];
        $status = $data['status'];

        // 3. 更新数据，判断显示状态，如果为1则更改为0，如果为0则更改为1
        try {
            if ( $status == 1 ) {
                SettlementModel::where('id', $id)
                    ->data('status', 0)
                    ->update();
            } else {
                SettlementModel::where('id', $id)
                    -> data('status', 1)
                    -> update();
            }
        } catch (\Exception $e) {
            return resMsg(0, '<i class="iconfont">&#xe646;</i> 操作失败，请检查' . '<br>' . $e->getMessage(), 'index' );
        }
        return resMsg(1, '<i class="iconfont">&#xe645;</i> 状态变更成功', 'index');
    }

    // 分配工单页面
    public function assignWorkorder(){
        // 获取分类id
        $settlementId = Request::param('id');

        // 根据分类id查询类别信息
        $settlementInfo = SettlementModel::where('id', $settlementId) -> find();
        // 获取工单列表
        $wolist = WorkorderModel::where('status','<>',3)->select();
        if(empty($wolist)){
            return ['status' => 0, 'message' => '不存在待结算工单'];
        }
        $k=0;
        foreach($wolist as $k=>$v){
            $nodes[$k]["id"]=$v["id"];
            $nodes[$k]["pid"]=$v["org_id"]+99999;
            $nodes[$k]["name"]=$v["id"]."-".$v["items"]["catagory"]["name"]."-".$v["items"]["code"];
        }
        $orglist = OrgModel::where('status',1)->where('pid',0)->select();
        foreach($orglist as $org){
            $nodes[++$k]["id"]=$org["id"] +99999;
            $nodes[$k]["pid"]=0;
            $nodes[$k]["name"]=$org["name"];
        }
        // 调用think\facade\Tree自定义无限级分类方法
        $nodes = Tree::createTree($nodes);

        $json = array();  // $json用户存放最新数组，里面包含当前分类是否有相应的机构
        $rules = explode('|', $settlementInfo['workorderlist']);
        foreach ($nodes as $node) {
            $res = in_array($node['id'], $rules);
            $data = array(
                'nid' => $node['id'],
//                'checked' => $node['id'],
                'parentid' => $node['pid'],
                'name' => $node['name'],
//                'id' => $node['id'] . '_' . $node['level'],
                'id' => $node['id'],
                'checked' => $res ? true : false
            );
            $json[] = $data;
        }

        // 5. 设置模板变量
        $this -> view -> assign('title', '关联机构');
        $this -> view -> assign('settlementInfo', $settlementInfo);
        $this -> view -> assign('json', json_encode($json));
        $this -> view -> assign('settlementId', $settlementId);

        // 渲染模板
        return $this -> view -> fetch('assignworkorder');
    }
    // 为结算单分配工单
    public function doAssign()
    {
        if ( Request::isAjax() ) {
            // 1. 获取的用户提交的信息
            $data = Request::post();
            // 2. 取出数据
            $id = $data['id'];
            $rules = $data['rules'];

            // 3. 变更当前类别关联的机构
            if ( isset($rules) ) {
                $datas = '';
                foreach ( $rules as $value ) {
                    $tmp = explode('|', $value);
                    if( $tmp[0]>=99999){
                        continue;
                    }
                    $datas .= '|';
                    $datas .= $tmp[0];
                }
                $datas = substr($datas, 1);
                $res = SettlementModel::where('id', $id) -> update(['workorderlist' => $datas]);
                if ( true == $res ) {
                    return ['status' => 1, 'message' => '关联工单操作成功', 'url' => 'index'];
                }
                return ['status' => 0, 'message' => '关联工单操作失败，请检查'];
            } else {
                return ['status' => 0, 'message' => '未接收到工单数据，请检查'];
            }
        } else {
            $this -> error("请求类型错误");
        }

    }
}