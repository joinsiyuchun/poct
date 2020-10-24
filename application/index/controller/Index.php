<?php


namespace app\index\controller;


use app\admin\common\controller\Base;
use app\index\common\model\Item as ItemModel;
use app\index\common\model\Workorder as WorkorderModel;
use rbac\Rbac;
use think\Db;
use think\facade\Session;
use think\facade\Request;
use app\admin\common\model\Admin;



class Index extends Base
{
    // 后台管理首页
    public function index()
    {
        // 实例化RBAC类
        $rbac = Rbac::instance();

        // 根据角色获取菜单
        $menu = $rbac -> getAuthMenu(Session::get('admin_role_id'));

        // 设置模板变量
        $this -> view -> assign('title', '医析医疗设备智慧管理系统');
        $this -> view -> assign('menu', $menu);

        // 渲染模板
        return $this -> fetch('index');
    }

    // 后台管理控制台
    public function home()
    {

        $sql = <<<SQL
SELECT 
    b.name AS name,
    COUNT(1) AS total,
    IFNULL(SUM(c.is_halt), 0) AS halt
FROM
    think_item AS a
        INNER JOIN
    think_catagory AS b ON a.catagoryid = b.id
        LEFT JOIN
    (SELECT 
        d.item_id, d.is_halt
    FROM
        think_workorder AS d
    WHERE
        (d.status = 0 OR d.status = 1)
            AND d.is_halt = 1
    GROUP BY d.item_id) AS c ON a.id = c.item_id
WHERE
    a.status = 1 AND a.pid = 0
GROUP BY a.catagoryid , b.name
order by halt desc
limit 16

SQL;
        $result = Db::query($sql);
        $list1=[];
        $list2=[];
        foreach($result as $k=>$v){
            if($k<8){
              $list1[$k]=$v;
            }else{
              $list2[$k-8]=$v;
            }
        }
        //outbound
        $waiting = WorkorderModel::where('status','0')->count(1);
        $ongoing = WorkorderModel::where('status','1')->count(1);
        $unpay = WorkorderModel::where('status','2')->count(1);
        //asset
        $total_items = ItemModel::where(['status'=>1,'pid'=>0])
            -> count();
        $cur_items = ItemModel::where(['status'=>1,'pid'=>0])->whereTime('start_date','y')
            -> count();
        $total_amount=ItemModel::where(['status'=>1,'pid'=>0])->sum('purchase_price');
        $cur_amount=ItemModel::where(['status'=>1,'pid'=>0])->whereTime('start_date','y')->sum('purchase_price');
        // 设置模板变量
        $this -> view -> assign('title', '医析医疗设备智慧管理系统');
        $this -> view -> assign('list1', $list1);
        $this -> view -> assign('list2', $list2);
        $this -> view -> assign('waiting', $waiting);
        $this -> view -> assign('ongoing', $ongoing);
        $this -> view -> assign('unpay', $unpay);
        $this -> view -> assign('total_items', $total_items);
        $this -> view -> assign('cur_items', $cur_items);
        $this -> view -> assign('total_amount', $total_amount);
        $this -> view -> assign('cur_amount', $cur_amount);
        $this -> view -> assign('title', '控制台');
        // 渲染模板
        return $this -> view -> fetch('home');

    }

    // 后台管理员修改密码
    public function editPassword()
    {
        // 获取当前管理员信息
        $id = Session::get('admin_id');
        $username = Session::get('admin_username');

        // 设置模板变量
        $this -> view -> assign([
            'id' => $id,
            'username' => $username,
            'title' => '修改密码'
        ]);

        // 渲染模板
        return $this -> view -> fetch('editpassword');
    }

    // 执行修改密码操作
    public function doEditPwd()
    {
        // 获取的用户提交的信息
        $data = Request::param();

        // 判断原密码是否正确
        $password = Admin::where('id', $data['id']) -> find()['password'];

        if ( !checkPassword($data['original_password'], $password) ) {
            return resMsg(-1, '原密码错误，请重新输入', 'editPassword');
        }

        $datas = [
            'password' => makePassword($data['password']),
            'update_time' => time()
        ];

        // 执行密码修改操作
        try {
            $admin = Admin::where('id', $data['id']) -> update($datas);
        } catch (\Exception $e) {
            return resMsg(0, '密码修改失败' . '<br>' . $e->getMessage(), 'edit' );
        }
        return resMsg(1, '密码修改成功', 'index');
    }
}