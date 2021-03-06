<?php


namespace app\index\controller;


use app\admin\common\controller\Base;
use app\admin\common\model\Org as OrgModel;
use app\index\common\model\Catagory as CatagoryModel;
use app\index\common\model\CatagoryPricelist;
use app\index\common\model\OrgCatagory as OrgCatagoryModel;
use app\index\common\model\Pricelist as PricelistModel;
use think\Db;
use think\facade\Request;
use think\facade\Tree;


class Catagory extends Base
{
    // 产品目录管理首页
    public function index()
    {
        $this -> view -> assign('title', '产品类别管理');
        return $this -> view -> fetch('index');
    }

    // 产品目录列表
    public function catagoryList()
    {
        $map = [];
        // 搜索功能
        $keywords = Request::param('keywords');
        if ( !empty($keywords) ) {
            $map[] = ['name', 'like', '%'.$keywords.'%'];
        }

        // 定义分页参数
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;

        // 获取产品目录信息
        $catagoryList = CatagoryModel::where($map)
            -> page($page, $limit)
            -> order('id', 'desc')
            -> select()
            ->toArray();
        $total = CatagoryModel::where($map)->count('id');

        $result = array("code" => 0, "msg" => "查询成功", "count" => $total, "data" => $catagoryList);
        return json($result);
    }

    public function pricelistPage()
    {
        // 获取分类id
        $catagoryId = Request::param('id');
        $this -> view -> assign('catagoryId', $catagoryId);
        return $this -> view -> fetch('pricelist');
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

    public function saveCategoryPrice()
    {
        $res = 0;
        $categoryId = Request::param('categoryId');
        $priceIds = Request::param('priceIds', []);
        $pageIds = rtrim( Request::param('pageIds', ''), ',');
        $pageIdsArr = explode(',', $pageIds);
        if(empty($categoryId) || (empty($priceIds) && empty($pageIds))){
            return json(['code' => $res, "msg" => '数据不能为空']);
        }

        CatagoryPricelist::where('category_id', $categoryId)
            ->whereIn('pricelist_id', $pageIds)
            ->delete();

        $time = time();
        $saveData = [];
        foreach ($priceIds as $priceId){
            $saveData[] = [
                'category_id' => (int)$categoryId,
                'pricelist_id' => (int)$priceId,
                'create_time' => $time,
                'update_time' => $time,
            ];
        }
        $res = Db::table(CatagoryPricelist::TABLE_NAME)->insertAll($saveData);
        return json(['code' => (int)$res, "msg" => $res ? '关联成功' : '取关成功']);
    }
    // 添加产品目录
    public function add()
    {
        $this -> view -> assign('title', '添加产品分类');
        return $this -> view -> fetch('add');
    }

    // 执行产品目录添加
    public function doAdd()
    {
        // 获取的用户提交的信息
        $data = Request::param();
        // 执行添加操作
        try {
            $catagory = CatagoryModel::where('name', $data['name']) -> find();
            if ( !empty($Catagory)) {
                return resMsg(-1, '产品目录已经存在，不能重复添加', 'add');
            }
            CatagoryModel::create($data);
        } catch (\Exception $e) {
            return resMsg(0, '产品目录添加失败' . '<br>' . $e->getMessage(), 'add' );
        }
        return resMsg(1, '产品目录添加成功', 'index');
    }

    // 编辑产品目录页面
    public function edit()
    {
        // 获取产品目录id
        $catagoryId = Request::param('id');

        // 根据产品目录id查询要更新的产品目录信息
        $catagoryInfo = CatagoryModel::where('id', $catagoryId) -> find();

        // 设置模板变量
        $this -> view -> assign('title', '编辑产品目录信息');
        $this -> view -> assign('catagoryInfo', $catagoryInfo);

        // 渲染模板
        return $this -> view -> fetch('edit');
    }

    // 执行编辑产品目录操作
    public function doEdit()
    {
        // 1. 获取的用户提交的信息
        $data = Request::param();

        // 执行编辑操作
        try {
            $catagory = CatagoryModel::where('name', $data['name']) -> where('id', '<>', $data['id']) -> find();
            if ( !empty($catagory)) {
                return resMsg(-1, '产品目录已经存在，请重新修改', 'edit');
            }
            CatagoryModel::update($data);
        } catch (\Exception $e) {
            return resMsg(0, '产品目录编辑失败' . '<br>' . $e->getMessage(), 'edit' );
        }
        return resMsg(1, '产品目录编辑成功', 'index');
    }

    // 删除产品目录
    public function del()
    {
        $id = Request::param('id');

        // 执行删除操作
        try {
            CatagoryModel::where('id', $id) -> delete();
        } catch (\Exception $e) {
            return resMsg(0, '产品目录删除失败' . '<br>' . $e->getMessage(), 'index' );
        }
        return resMsg(1, '产品目录删除成功', 'index');
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
                CatagoryModel::where('id', $id)
                    ->data('status', 0)
                    ->update();
            } else {
                CatagoryModel::where('id', $id)
                    -> data('status', 1)
                    -> update();
            }
        } catch (\Exception $e) {
            return resMsg(0, '<i class="iconfont">&#xe646;</i> 操作失败，请检查' . '<br>' . $e->getMessage(), 'index' );
        }
        return resMsg(1, '<i class="iconfont">&#xe645;</i> 状态变更成功', 'index');
    }

    // 角色授权页面
    public function auth(){
        // 获取分类id
        $catagoryId = Request::param('id');

        // 根据分类id查询类别信息
        $catagoryInfo = CatagoryModel::where('id', $catagoryId) -> find();

        // 获取机构列表
        $nodes = OrgModel::select();
        // 调用think\facade\Tree自定义无限级分类方法
        $nodes = Tree::createTree($nodes);

        $json = array();  // $json用户存放最新数组，里面包含当前分类是否有相应的机构
        $rules = explode(',', $catagoryInfo['rules']);
        foreach ($nodes as $node) {
            $res = in_array($node['id'], $rules);
            $data = array(
                'nid' => $node['id'],
                'checked' => $node['id'],
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
        $this -> view -> assign('catagoryInfo', $catagoryInfo);
        $this -> view -> assign('json', json_encode($json));
        $this -> view -> assign('catagoryId', $catagoryId);

        // 渲染模板
        return $this -> view -> fetch('auth');
    }


    // 处理角色授权 添加角色-权限表
    public function doAuth()
    {
        if ( Request::isAjax() ) {
            // 1. 获取的用户提交的信息
            $data = Request::post();
            // 2. 取出数据
            $id = $data['id'];
            $rules = $data['rules'];
            OrgCatagoryModel::where("catagory_id",$id)->delete();
            // 3. 变更当前类别关联的机构
            if ( isset($rules) ) {
                $datas = '';
                foreach ( $rules as $value ) {
                    $tmp = explode('_', $value);
                    $datas .= ',';
                    $datas .= $tmp[0];
                    //add relation
                    $orgcatmodel= new OrgCatagoryModel;
                    $orgcatmodel->save(['org_id'=>$value,'catagory_id'=>$id,'status'=>1]);
                }
                $datas = substr($datas, 1);
                $res = CatagoryModel::where('id', $id) -> update(['rules' => $datas]);
                if ( true == $res ) {
                    return ['status' => 1, 'message' => '规则机构操作成功', 'url' => 'index'];
                }
                return ['status' => 0, 'message' => '关联机构操作失败，请检查'];
            } else {
                return ['status' => 0, 'message' => '未接收到机构数据，请检查'];
            }
        } else {
            $this -> error("请求类型错误");
        }

    }
}
