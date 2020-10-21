<?php


namespace app\index\controller;

use app\index\common\model\Org as OrgModel;
use app\common\library\facade\Setting as Setting;
use app\admin\common\controller\Base;
use app\index\common\model\Catagory as CatagoryModel;
use app\index\common\model\Item as ItemModel;
use think\facade\Request;
use think\facade\Tree;

class Item extends Base
{
    // item管理首页
    public function index()
    {
        $this -> view -> assign('title', '设备档案');
        return $this -> view -> fetch('index');
    }

    // item列表
    public function itemList()
    {
        $map = [];
        $itemList = [];
        $keywords = Request::param('keywords');
        if ( !empty($keywords) ) {
            $map[] = ['code', 'like', '%' . $keywords . '%'];
        }

        $items = ItemModel::where($map)
            -> order('sort', 'asc')
            -> field('id, code, sn,pn, pid, sort, catagoryid, is_kit, status, create_time, update_time,is_backup')
            -> select();
//            -> toArray();
        foreach($items as $i=>$v){
            $itemList[$i]["id"]=$v["id"];
            $itemList[$i]["code"]=$v["code"];
            $itemList[$i]["sn"]=$v["sn"];
            $itemList[$i]["pn"]=$v["pn"];
            $itemList[$i]["pid"]=$v["pid"];
            $catagory=$v->catagory;
            $itemList[$i]["title"]=$catagory["name"];
            $itemList[$i]["is_kit"]=$v["is_kit"];
            $itemList[$i]["status"]=$v["status"];
            $itemList[$i]["create_time"]=$v["create_time"];
            $itemList[$i]["update_time"]=$v["update_time"];
            $itemList[$i]["is_backup"]=$v["is_backup"];
        }

        $total = count($itemList);
        $result = array("code" => 0, "count" => $total, "data" => $itemList);
        return json($result);
    }


    public function itemListbyaddcost()
    {
        $map = [];
        $itemList=[];
        $keywords = Request::param('keywords');
        if ( !empty($keywords) ) {
            $map[] = ['code', 'like', '%' . $keywords . '%'];
        }

        $items = ItemModel::where($map)
            ->where('status',1)
            ->where('pid',0)
            -> order('sort', 'asc')
            -> field('id, code,location,brand,model, sn,pn, pid, sort,purchase_price,start_date, catagoryid, is_kit, status, create_time, update_time,is_backup')
            -> select();
//            -> toArray();
        foreach($items as $i=>$v){
            $itemList[$i]["id"]=$v["id"];
            $itemList[$i]["code"]=$v["code"];
            $itemList[$i]["location"]=$v["location"];
            $itemList[$i]["brand"]=$v["brand"];
            $itemList[$i]["model"]=$v["model"];
            $itemList[$i]["sn"]=$v["sn"];
            $itemList[$i]["pn"]=$v["pn"];
            $itemList[$i]["pid"]=$v["pid"];
            $catagory=$v->catagory;
            $itemList[$i]["title"]=$catagory["name"];
            $itemList[$i]["is_kit"]=$v["is_kit"];
            $itemList[$i]["status"]=$v["status"];
            $itemList[$i]["purchase_price"]=$v["purchase_price"];
            $itemList[$i]["start_date"]=$v["start_date"];
            $itemList[$i]["create_time"]=$v["create_time"];
            $itemList[$i]["update_time"]=$v["update_time"];
            $itemList[$i]["is_backup"]=$v["is_backup"];
        }

        $total = count($itemList);
        $result = array("code" => 0, "count" => $total, "data" => $itemList);
        return json($result);
    }
    // 添加item
    public function add()
    {
        $pid = Request::param('pid');
        $parentItem = Request::param('parentItem');

        if ( $pid == 0 ) {
            $title = "添加顶级item";
        } else {
            $title = "添加子item";
        }
        $nodes = CatagoryModel::where('status',1)
            -> order('id', 'desc')
            -> field('id, name')
            -> select();
        $this -> view -> assign(
            [
                'title' => $title,
                'pid' => $pid,
                'catagorylist' => $nodes,
                'parentItem' => $parentItem
            ]
        );
        return $this -> view -> fetch('add');
    }



    // 执行添加节点操作
    public function doAdd()
    {
        // 获取用户提交的信息
        $data = Request::param();

        // 执行添加操作
        try {
            $item = ItemModel::where('code', $data['code']) -> find();
            if ( !empty($role)) {
                return resMsg(-1, '设备编码已经存在，不能重复添加', 'add');
            }
            $ret=ItemModel::create($data);
            $this->genenateQrcode($ret['id']);
        } catch (\Exception $e) {
            return resMsg(0, '设备添加失败' . '<br>' . $e->getMessage(), 'add' );
        }
        return resMsg(1, '设备添加成功', 'index');
    }

    // 编辑节点
    public function edit()
    {
        // 获取节点id
        $itemId = Request::param('id');

        // 根据节点id查询要更新的节点信息
        $itemInfo = ItemModel::where('id', $itemId) -> find();

        // 根据父ID获取父节点名称
        if ( $itemInfo['pid'] == 0 ) {
            $parentItem = '顶级节点';
        } else {
            $parentItem = ItemModel::where('id', $itemInfo['pid']) -> field('code') -> find()['code'];
        }
        //设置设备分类下拉框
        $nodes = CatagoryModel::where('status',1)
            -> order('id', 'desc')
            -> field('id, name')
            -> select();
        // 设置模板变量
        $this -> view -> assign('title', '编辑设备');
        $this -> view -> assign('itemInfo', $itemInfo);
        $this -> view -> assign('parentItem', $parentItem);
        $this -> view -> assign('catagorylist', $nodes);
        // 渲染模板
        return $this -> view -> fetch('edit');
    }

    // 执行编辑节点操作
    public function doEdit()
    {
        // 1. 获取的用户提交的信息
        $data = Request::param();

        // 执行编辑操作
        try {
            $item = ItemModel::where('code', $data['code']) -> where('id', '<>', $data['id']) -> find();
            if ( !empty($item)) {
                return resMsg(0, '设备编码已经存在，请重新修改', 'edit');
            }
            ItemModel::update($data);
        } catch (\Exception $e) {
            return resMsg(0, '设备编辑失败' . '<br>' . $e->getMessage(), 'edit' );
        }
        return resMsg(1, '设备编辑成功', 'index');
    }

    // 删除节点
    public function delete()
    {
        if ( Request::isAjax() ) {
            // 执行删除操作
            try {
                $id = Request::param('id');
                ItemModel::where('id', $id) -> delete();
            } catch (\Exception $e) {
                return resMsg(0, '设备删除失败' . '<br>' . $e->getMessage(), 'index' );
            }
            return resMsg(1, '设备删除成功', 'index');
        } else {
            return resMsg(-1, '请求类型错误', 'index');
        }
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
                ItemModel::where('id', $id)
                    ->data('status', 0)
                    ->update();
            } else {
                ItemModel::where('id', $id)
                    -> data('status', 1)
                    -> update();
            }
        } catch (\Exception $e) {
            return resMsg(0, '<i class="iconfont">&#xe646;</i> 操作失败，请检查' . '<br>' . $e->getMessage(), 'index' );
        }
        return resMsg(1, '<i class="iconfont">&#xe645;</i> 状态变更成功', 'index');
    }

    // 变更状态
    public function setBackup()
    {
        // 1. 获取用户提交的数据
        $data = Request::param();

        // 2. 取出数据
        $id = $data['id'];
        $status = $data['status'];

        // 3. 更新数据，判断显示状态，如果为1则更改为0，如果为0则更改为1
        try {
            if ( $status == 1 ) {
                ItemModel::where('id', $id)
                    ->data('is_backup', 0)
                    ->update();
            } else {
                ItemModel::where('id', $id)
                    -> data('is_backup', 1)
                    -> update();
            }
        } catch (\Exception $e) {
            return resMsg(0, '<i class="iconfont">&#xe646;</i> 操作失败，请检查' . '<br>' . $e->getMessage(), 'index' );
        }
        return resMsg(1, '<i class="iconfont">&#xe645;</i> 状态变更成功', 'index');
    }

    // 下载二维码
    public function getQrcode($id)
    {
        // 1. 获取access token
        $appid = Setting::get('appid');
        $secret = Setting::get('appsecret');
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $secret ;
        $tkdata = json_decode($this->request($url, 'GET'), true);
        $accesstoken=$tkdata['access_token'];
        //2. 获取qrcode
        try {
            $url2 = 'https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token=' . $accesstoken;
            $param['path']="pages/itempage/index?id=".$id;
            $param['width']=430;
            $qrdata =$this->request($url2, 'POST',$param);
            $file =$id.".jpg";
            file_put_contents('./static/uploads/'.$file,$qrdata);
        } catch (\Exception $e) {
            return resMsg(0, '<i class="iconfont">&#xe646;</i> 操作失败，请检查' . '<br>' . $e->getMessage(), 'index' );
        }
        return resMsg(1, '<i class="iconfont">&#xe645;</i> 下载成功', 'index');
    }

    // 下载二维码
    public function genenateQrcode($id)
    {

        // 1. 获取access token
        $appid = Setting::get('appid');
        $secret = Setting::get('appsecret');
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $secret ;
        $tkdata = json_decode($this->request($url, 'GET'), true);
        $accesstoken=$tkdata['access_token'];
        // 2. 获取qrcode
        try {
            $url2 = 'https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token=' . $accesstoken;
            $param['path']="pages/itempage/index?id=".$id;
            $param['width']=430;
            $qrdata =$this->request($url2, 'POST',$param);
            $file =$id.".jpg";
            file_put_contents('./static/uploads/'.$file,$qrdata);
        } catch (\Exception $e) {
            return resMsg(0, '<i class="iconfont">&#xe646;</i> 操作失败，请检查' . '<br>' . $e->getMessage(), 'index' );
        }
        return resMsg(1, '<i class="iconfont">&#xe645;</i> 下载成功', 'index');
    }

    //批量分配组织
    public function assignorg()
    {
        // 获取节点id
        $ids = Request::param('id');


        //设置组织下拉框
        $nodes = OrgModel::where('status',1)
            -> order('id', 'desc')
            -> field('id, name, pid')
            -> select();
        $nodes = Tree::createTree($nodes);
        $json = array();  // $json用户存放最新数组，里面包含当前用户组是否有相应的权限

        foreach ($nodes as $node) {

            $data = array(
                'nid' => $node['id'],
                'checked' => $node['id'],
                'parentid' => $node['pid'],
                'name' => $node['name'],
                'id' => $node['id'],
                'checked' => false
            );
            $json[] = $data;
        }
        // 设置模板变量
        $this -> view -> assign('title', '分配组织');
        $this -> view -> assign('ids', $ids);
        $this -> view -> assign('json', json_encode($json));
        // 渲染模板
        return $this -> view -> fetch('assignorg');
    }

    public function assignSingleOrg(){
        // 获取组织id
        $itemId = Request::param('id');

        // 根据组织id查询组织信息
        $itemInfo = ItemModel::where('id', $itemId) -> find();

        // 获取权限列表
        $nodes = OrgModel::order('sort', 'asc') -> select();
        // 调用think\facade\Tree自定义无限级分类方法
        $nodes = Tree::createTree($nodes);

        $json = array();  // $json用户存放最新数组，里面包含当前用户组是否有相应的权限
        $rules = explode('|', $itemInfo['org_list']);
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
        $this -> view -> assign('title', '分配组织');
        $this -> view -> assign('itemInfo', $itemInfo);
        $this -> view -> assign('json', json_encode($json));
        $this -> view -> assign('itemId', $itemId);

        // 渲染模板
        return $this -> view -> fetch('item/assignsingleorg');
    }

    // 执行编辑节点操作
    public function doAssign()
    {
        // 1. 获取的用户提交的信息
//        $data = Request::param();
        // 1. 获取的用户提交的信息
        $data = Request::post();
        // 2. 取出数据
        $ids = $data['ids'];
        $orglist = $data['org_list'];
        $ids_arr = explode('|',$ids);
        try {
            foreach ($ids_arr as $id) {
                if ($id == 'on') {
                    continue;
                }
                if (isset($orglist)) {
                    $datas = join($orglist, "|");
                    $res = ItemModel::where('id', $id)->update(['org_list' => $datas]);
                }
            }
        }catch (\Exception $e) {
            return resMsg(0, '设备编辑失败' . '<br>' . $e->getMessage(), 'assignorg' );
        }
        return resMsg(1, '组织分配成功', 'assignorg');

    }
}