<?php
namespace app\index\controller;
use app\tools\AdminController;
use app\index\model\User as UserModel;
use app\tools\UserToken;
use app\index\model\UserHealth;
use app\index\model\UserEstimate;

class User extends AdminController{
    // 用户登录
    public function login(){
        $data = processRequest();
        $result = $this->validate($data, 'app\index\validate\User');
        if($result !== true){
            return jsonAPI($result, 400);
        }
        $user = UserModel::where("username",$data["username"])->find();
        if($user == null){
            return jsonAPI("用户名不存在！", 400);
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
}