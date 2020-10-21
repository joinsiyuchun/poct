<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\common\library\facade\Setting as Setting;
use app\api\common\model\User as UserModel;
use app\api\common\model\Org as OrgModel;
use app\api\common\model\UserOrg as UserOrgModel;
use think\facade\Session;

class User extends Api {

    protected $checkLoginExclude = ['setting', 'login'];

    public function setting() {
        return json(['isLogin' => Session::has('user')]);
    }


    public function login() {
        $js_code = $this->request->get('js_code/s', '');
        $appid = Setting::get('appid');
        $secret = Setting::get('appsecret');
        $url = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $appid . '&secret=' . $secret . '&js_code=' . $js_code . '&grant_type=authorization_code';
        $data = json_decode($this->request($url, 'GET'), true);
        if (isset($data['openid'])) {
            $openid = $data['openid'];
            $user = UserModel::get(['openid' => $openid]);
            if (!$user) {
                $user = UserModel::create(['openid' => $openid]);
            }
            $org=UserOrgModel::get(['user_id'=>(int) $user->id,'status'=>1]);
//            $realname=$user["user_name"];
//            if($realname==null){
//                $realname=$user->id;
//            }
       //     Session::set('user', ['id' => (int) $user->id, 'openid' => $openid, 'realname' => $realname]);
            Session::set('user', ['id' => (int) $user->id, 'openid' => $openid]);
            if($org!=null){
                $orgid=$org->org_id;
                Session::set('org', ['id' => $orgid]);
            }else{
//                $data1 = [
//                    'user_id' => (int)$user->id,
//                    'org_id' => 2,
//                    'status' => 1,
//                    'expire_time' => strtotime('+1 year')
//                ];
//                UserOrgModel::create($data1);
                Session::set('org', ['id' =>1]);
            }
            return json(['isLogin' => true]);
        }
        return json(['isLogin' => false]);
    }

    public function update()
    {
        $username = $this->request->post('username/s', '');
        $gender= $this->request->post('gender/s', '');
        $data = [
            'id' =>  $this->user['id'],
            'user_name' => $username,
            'gender' => $gender
        ];
        UserModel::update($data);
        $this->success('修改成功');
    }

    public function detail()
    {
            $user = UserModel::field('user_name,gender')->where(['id' => $this->user['id']])->find();
            
            return json($user);
    }

    public function company()
    {
        $companylist = [];
        $user = UserModel::get($this->user['id'], 'UserOrg');
        $orglist = $user["user_org"];
        if (!isset($orglist)) {
            $company = OrgModel::get(1);
            $companylist[0]['id'] = $company['id'];
            $companylist[0]['name'] = $company['name'];
        } else {
            foreach ($orglist as $k => $v) {
                $company = OrgModel::get($v['org_id']);
                $companylist[$k]['id'] = $company['id'];
                $companylist[$k]['name'] = $company['name'];
            }
        }
        return json($companylist);
    }

    public function companylist()
    {
        $companylist = [];
        $orglist = OrgModel::all();
        foreach($orglist as $k =>$v){
            $companylist['org_id'][$k]=$v['id'];
            $companylist['org_name'][$k]=$v['name'];
        }
        return json($companylist);
    }
    public function verify()
    {
        $org_id = $this->request->post('com/d', '');
        $verifycode= $this->request->post('no/s', '');
        if($verifycode=='123456') {
            $data = [
                'user_id' => $this->user['id'],
                'org_id' => $org_id
            ];
            $org=UserOrgModel::get(['user_id' => $this->user['id'], 'org_id' => $org_id]);
            if($org!=null){
                $this->success('权限已存在');
            }else{
                $data1 = [
                    'user_id' => $this->user['id'],
                    'org_id' => $org_id,
                    'status'=>0,
                    'start_date'=>date("Y-m-d h:i:s"),
                    'expired_date'=>date ( "Y-m-d h:i:s" , strtotime ( '+1 year' ))
                ];
                UserOrgModel::create($data1);
            }
            $this->success('提交成功');
        }else{
             $this->success('验证码错误');
        }

    }

    public function switchhospital()
    {
        $org_id = $this->request->get('id/d', '');
        if($org_id != null){
            UserOrgModel::where(['user_id'=>$this->user['id']])->update(['status' => 0]);
            $data = [
                'user_id' =>  $this->user['id'],
                'org_id' =>  $org_id
            ];
            UserOrgModel::where($data)->update(['status' => 1]);
            Session::set('org', ['id' => $org_id]);
            $this->success('提交成功');
        }
    }

}
