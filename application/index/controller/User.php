<?php
namespace app\index\controller;
use app\tools\AdminController;
use app\index\model\User as UserModel;
use app\tools\UserToken;
use app\index\model\UserHealth;
use app\index\model\UserEstimate;
use app\index\model\Role;

class User extends AdminController{
    // 用户登录
    public function login(){
        $data = processRequest();
        $result = $this->validate($data, 'app\index\validate\User');
        if($result !== true){
            return jsonAPI($result, 400);
        }
        $user = UserModel::where([
                "username" => $data["username"],
                "status" => 1
            ])->find();
        if($user == null){
            return jsonAPI("用户名不存在或账户不可用！", 400);
        }
        if($user->password !== md5($data["password"])){
            return jsonAPI("用户名或者密码错误！", 400);
        }
        // 返回token
        $usertoken = new UserToken();
        $token = $usertoken->getToken($user);
        
        $data = [
            "id" => $user->user_id,
            "token" => $token
        ];
        return jsonAPI("登录成功！", 200, $data);
    }

    // 用户注册
    public function register(){
        $data = processRequest();
        $result = $this->validate($data, 'app\index\validate\User');
        if($result !== true){
            return jsonAPI($result, 422);
        }
        if(!isset($data["repassword"]) || $data["repassword"] !== $data["password"]){
            return jsonAPI("两次密码不一致！", 422);
        }
        $user = new UserModel([
            "username" => $data["username"],
            "password" => md5($data["password"])
            ]);
        if($user->save()){
            $userEstimate = new UserEstimate([
                    "user_id" => $user->user_id
                ]);
            if($userEstimate->save()){
                return jsonAPI("创建成功！", 200);
            }
        }
        return jsonAPI("创建失败！", 400); 
    }

    // 用户名检测
    public function check(){
        $data = processRequest();
        if(!isset($data["username"])){
            return jsonAPI("请求参数为空！", 500);
        }
        $username = UserModel::where("username", $data["username"])->find();
        if(!$username){
            return jsonAPI("用户名不存在！", 200);
        }
        return jsonAPI("用户名已存在！", 500);
    }

    // 查询用户列表
    public function list(){
        $data = processRequest();
        // 获取当前页码和每页条数
        if(!isset($data["pagenum"]) || !isset($data["pagesize"])){
            return jsonAPI("查询参数为空!", 400);
        }
        $pagenum = $data["pagenum"];
        $pagesize = $data["pagesize"];
        $user = new UserModel();

        if(isset($data["query"])){
            //根据用户名查询用户
            $name = $data["query"];
            $total = $user->where("username", "like", "%".$name."%")->count();
            $list = $user->where("username", "like", "%".$name."%")->page($pagenum, $pagesize)->select();
            $info = $user->getList($list);
        }else{
            // 查询所有用户
            $total  = $user->count();
            $list = $user->page($pagenum, $pagesize)->select();
            $info = $user->getList($list);
        }
        $data = [
            "total" => $total,
            "pagenum" => $pagenum,
            "users" => $info
        ];
        return jsonAPI("查询成功！", 200, $data);
    }

    // 获取所有角色名称
    public function rolelist(){
        $role = new Role();
        $role_list = $role->select();
        $data = array();
        foreach ($role_list as $key => $value) {
            $data[] = [
                "role_id" => $value->role_id,
                "role_name" => $value->role_name
            ];
        }
        return jsonAPI("查询成功！", 200, $data);
    }

    // 分配用户角色
    public function role(){
        $data = processRequest();
        if(!isset($data["user_id"]) || !isset($data["role_id"])){
            return jsonAPI("请求参数为空！", 500);
        }
        $user = UserModel::where("user_id", $data["user_id"])->find();
        if(!$user){
            return jsonAPI("用户不存在！", 500);
        }
        $user->role_id = $data["role_id"];
        if($user->save()){
            return jsonAPI("角色分配成功！", 200);
        }
        return jsonAPI("角色分配失败！", 500);
    }

    // 修改用户状态
    public function status(){
        $data = processRequest();
        if(!isset($data["user_id"]) || !isset($data["status"])){
            return jsonAPI("请求参数为空！", 500);
        }
        $status = $data["status"];
        if($status !== 'false'){
            $type = 1;
        }else{
            $type = 0;
        }
        $user = UserModel::where("user_id", $data["user_id"])->find();
        if(!$user){
            return jsonAPI("用户不存在！", 500);
        }
        $user->status = $type;
        if($user->save()){
            return jsonAPI("角色状态修改成功！", 200);
        }
        return jsonAPI("角色状态修改失败！", 500);
    }
}