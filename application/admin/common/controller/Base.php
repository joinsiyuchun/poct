<?php


namespace app\admin\common\controller;


use think\Controller;
use think\facade\Request;
use think\facade\Session;
use rbac\Rbac;

class Base extends Controller
{
    /**
     * 初始化方法
     * 创建常量、公共方法
     * 在所有的方法之前被调用
     */
    protected function initialize()
    {
        // 检测session中是否存在管理员信息
        if ( !Session::has('admin_id') ) {
            $this -> redirect('login/index');
        }

        // 获取控制器和方法
        $controller = lcfirst(Request::controller());
        $action = Request::action();
        $module = Request::module();
        $url = $module . '/' . $controller . '/' . $action;

        // 实例化RBAC类
        $rbac = Rbac::instance();

        // 设置无需验证的权限
        $skipMap = $rbac -> getSkipAuthMap();

        if ( !isset($skipMap[$url]) ) {
            $res = $rbac -> authCheck($url, Session::get('admin_role_id'));

            if ( !$res ) {
                if ( Request::isAjax() ) {
                    return json(resMsg(-403, '无操作权限', 'index'));
                } else {
                    $this -> error('无操作权限');
                }
            }
        }

        $this -> view -> assign([
            'admin_id'  => Session::get('admin_id'),
            'admin_username' => Session::get('admin_username')
        ]);
    }

    protected function request($url, $method, array $data=[] , array $headers = [])
    {
        $ci = curl_init();
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, false);
        switch (strtoupper($method)) {
            case 'POST':
                curl_setopt($ci, CURLOPT_POST, true);
                if (!empty($data)) {
                    curl_setopt($ci, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
        }
        curl_setopt($ci, CURLOPT_URL, $url);
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ci);
        if (curl_errno($ci)) {
            $response = 'Errno ' . curl_error($ci);
        }
        curl_close($ci);
        return $response;
    }
}