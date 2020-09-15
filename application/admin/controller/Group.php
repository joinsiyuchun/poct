<?php


namespace app\admin\controller;


use app\admin\common\controller\Base;
use app\admin\common\model\Group as GroupModel;
use think\facade\Request;
use think\facade\Tree;
use app\admin\common\model\Org as OrgModel;



class Group extends Base
{
    // 角色管理首页
    public function index()
    {
        $this -> view -> assign('title', '维修组管理');
        return $this -> view -> fetch('index');
    }

    // 角色列表
    public function GroupList()
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

        // 获取角色信息
        $groupList = GroupModel::where($map)
            -> page($page, $limit)
            -> order('id', 'desc')
            -> select();
        $total = count(GroupModel::where($map)->select());
        $result = array("code" => 0, "msg" => "查询成功", "count" => $total, "data" => $groupList);
        return json($result);

        // 3. 设置模板变量
        $this -> view -> assign('groupList', $groupList);

        // 4. 渲染模板
        return $this -> view -> fetch('index');
    }

    // 添加维修组
    public function add()
    {
        $this -> view -> assign('title', '添加维修组');
        return $this -> view -> fetch('add');
    }

    // 执行维修组添加
    public function doAdd()
    {
        // 获取的用户提交的信息
        $data = Request::param();

        // 执行添加操作
        try {
            $group = GroupModel::where('name', $data['name']) -> find();
            if ( !empty($group)) {
                return resMsg(-1, '维修组名称已经存在，不能重复添加', 'add');
            }
            $data['rules'] = '1';
            GroupModel::create($data);
        } catch (\Exception $e) {
            return resMsg(0, '维修组添加失败' . '<br>' . $e->getMessage(), 'add' );
        }
        return resMsg(1, '维修组添加成功', 'index');
    }

    // 编辑维修组页面
    public function edit()
    {
        // 获取维修组id
        $groupId = Request::param('id');

        // 根据维修组id查询要更新的维修组信息
        $groupInfo = GroupModel::where('id', $groupId) -> find();

        // 设置模板变量
        $this -> view -> assign('title', '编辑维修组');
        $this -> view -> assign('groupInfo', $groupInfo);

        // 渲染模板
        return $this -> view -> fetch('edit');
    }

    // 执行编辑维修组操作
    public function doEdit()
    {
        // 1. 获取的用户提交的信息
        $data = Request::param();

        // 执行编辑操作
        try {
            $group = GroupModel::where('name', $data['name']) -> where('id', '<>', $data['id']) -> find();
            if ( !empty($group)) {
                return resMsg(-1, '维修组名称已经存在，请重新修改', 'edit');
            }
            GroupModel::update($data);
        } catch (\Exception $e) {
            return resMsg(0, '维修组编辑失败' . '<br>' . $e->getMessage(), 'edit' );
        }
        return resMsg(1, '维修组编辑成功', 'index');
    }

    // 删除维修组
    public function del()
    {
        $id = Request::param('id');

        // 执行删除操作
        try {

            GroupModel::where('id', $id) -> delete();
        } catch (\Exception $e) {
            return resMsg(0, '维修组删除失败' . '<br>' . $e->getMessage(), 'index' );
        }
        return resMsg(1, '维修组删除成功', 'index');
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
                GroupModel::where('id', $id)
                    ->data('status', 0)
                    ->update();
            } else {
                GroupModel::where('id', $id)
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
        // 获取角色id
        $groupId = Request::param('id');

        // 根据角色id查询角色信息
        $groupInfo = GroupModel::where('id', $groupId) -> find();

        // 获取权限列表
        $nodes = OrgModel::select();
        // 调用think\facade\Tree自定义无限级分类方法
        $nodes = Tree::createTree($nodes);

        $json = array();  // $json用户存放最新数组，里面包含当前用户组是否有相应的机构
        $rules = explode(',', $groupInfo['rules']);
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
        $this -> view -> assign('groupInfo', $groupInfo);
        $this -> view -> assign('json', json_encode($json));
        $this -> view -> assign('groupId', $groupId);

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

            // 3. 变更当前角色拥有的权限规则
            if ( isset($rules) ) {
                $datas = '';
                foreach ( $rules as $value ) {
                    $tmp = explode('_', $value);
                    $datas .= ',';
                    $datas .= $tmp[0];
                }
                $datas = substr($datas, 1);
                $res = GroupModel::where('id', $id) -> update(['rules' => $datas]);
                if ( true == $res ) {
                    return ['status' => 1, 'message' => '规则机构操作成功', 'url' => 'index'];
                }
                return ['status' => 0, 'message' => '机构关联操作失败，请检查'];
            } else {
                return ['status' => 0, 'message' => '未接收到机构数据，请检查'];
            }
        } else {
            $this -> error("请求类型错误");
        }

    }
}