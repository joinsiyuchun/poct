<?php


namespace app\admin\controller;


use app\admin\common\controller\Base;
use app\admin\common\model\Role;
use app\admin\common\model\Group;
use think\db;
use think\facade\Request;
use app\admin\common\model\Admin as AdminModel;

class Admin extends Base
{
    // 用户管理首页
    public function index()
    {
        $this -> view -> assign('title', '用户管理');
        return $this -> view -> fetch('index');
    }

    // 用户列表
    public function adminList()
    {
        // 定义全局查询条件
        $map = []; // 将所有的查询条件封装到这个数组中

        // 搜索功能
        $keywords = Request::param('keywords');
        if ( !empty($keywords) ) {
            $map[] = ['username', 'like', '%'.$keywords.'%'];
        }

        // 定义分页参数
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
        $page = isset($_GET['page']) ? $_GET['page'] : 1;

        // 获取到所有的用户
        $adminList = AdminModel::where($map)
            -> alias('a')
            -> join(['think_role' => 'r'], 'a.role_id = r.id')
            -> join(['think_group' => 'f'], 'a.group_id = f.id')
            -> order('a.id', 'desc')
            -> field('a.id, a.username, a.role_id, a.group_id,a.status, a.create_time, a.update_time, a.last_login_time, r.name,f.name gname')
            -> select();

        $total = count(AdminModel::where($map)->select());
        $result = array("code" => 0, "msg" => "查询成功", "count" => $total, "data" => $adminList);
        return json($result);

        // 3. 设置模板变量
        $this -> view -> assign('adminList', $adminList);

        // 4. 渲染模板
        return $this -> view -> fetch('index');
    }

    // 添加用户
    public function add()
    {
        // 获取所有非超级用户角色
        $roleList = Role::where('id', '>', 1) -> select();
        // 获取所有维修组
        $groupList = Group::where('status', 1) -> select();

        // 设置模板变量
        $this -> view -> assign('title', '添加用户');
        $this -> view -> assign('roleList', $roleList);
        $this -> view -> assign('groupList', $groupList);

        // 渲染模板
        return $this -> view -> fetch('add');
    }

    // 执行用户添加操作
    public function doAdd()
    {
        // 获取用户提交的信息
        $data = Request::param();
        $data['password'] = makePassword($data['password']);

        // 执行添加操作
        try {
            $node = AdminModel::where('username', $data['username']) -> find();
            if ( !empty($node)) {
                return resMsg(-1, '用户名已经存在，不能重复添加', 'add');
            }
            AdminModel::create($data);
        } catch (\Exception $e) {
            return resMsg(0, '用户添加失败' . '<br>' . $e->getMessage(), 'add' );
        }
        return resMsg(1, '用户添加成功', 'index');
    }

    // 编辑用户
    public function edit()
    {
        // 获取节点id
        $adminId = Request::param('id');

        // 根据节点id查询要更新的节点信息
        $adminInfo = AdminModel::where('id', $adminId) -> field('id, username, role_id, group_id,status') -> find();

        // 获取所有非超级用户角色
        $roleList = Role::where('id', '>', 1) -> select();
        // 获取所有维修组
        $groupList = Group::where('status', 1) -> select();

        // 设置模板变量
        $this -> view -> assign('title', '编辑用户');
        $this -> view -> assign('adminInfo', $adminInfo);
        $this -> view -> assign('roleList', $roleList);
        $this -> view -> assign('groupList', $groupList);

        // 渲染模板
        return $this -> view -> fetch('edit');
    }

    // 执行编辑用户操作
    public function doEdit()
    {
        // 1. 获取的用户提交的信息
        $data = Request::param();

        if ( !empty($data['password']) ) {
            $data['password'] = makePassword($data['password']);
        } else {
            unset($data['password']);
        }

        $data['update_time'] = time();

        // 执行编辑操作
        try {
            $admin = AdminModel::where('username', $data['username']) -> where('id', '<>', $data['id']) -> find();
            if ( !empty($admin)) {
                return resMsg(-1, '用户名已经存在，请重新修改', 'edit');
            }
            AdminModel::update($data);
        } catch (\Exception $e) {
            return resMsg(0, '用户编辑失败' . '<br>' . $e->getMessage(), 'edit' );
        }
        return resMsg(1, '用户编辑成功', 'index');
    }

    // 删除用户
    public function delete()
    {
        if ( Request::isAjax() ) {
            // 执行删除操作
            try {
                $id = Request::param('id');
                AdminModel::where('id', $id) -> delete();
            } catch (\Exception $e) {
                return resMsg(0, '用户删除失败' . '<br>' . $e->getMessage(), 'index' );
            }
            return resMsg(1, '用户删除成功', 'index');
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
                AdminModel::where('id', $id)
                    ->data('status', 0)
                    ->update();
            } else {
                AdminModel::where('id', $id)
                    -> data('status', 1)
                    -> update();
            }
        } catch (\Exception $e) {
            return resMsg(0, '<i class="iconfont">&#xe646;</i> 操作失败，请检查' . '<br>' . $e->getMessage(), 'index' );
        }
        return resMsg(1, '<i class="iconfont">&#xe645;</i> 状态变更成功', 'index');
    }

    // 初始化超级用户密码，生产环境不使用时请注释
//    public function setPwd()
//    {
//        // 生成加密的密码
//        $pwd = makePassword('admin');
//
//        // 更新数据库存储的用户密码
//        $data = ['password' => $pwd];
//        $res = AdminModel::where('username', 'admin') -> update($data);
//        if ( !$res ) {
//            return $this -> error('密码重置失败');
//        }
//        return $this -> success('密码重置成功');
//    }
}