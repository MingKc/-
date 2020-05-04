<?php
namespace app\index\controller;
use app\tools\AdminController;
use app\index\model\User as UserModel;
use app\tools\UserToken;
use app\index\model\UserHealth;


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
        // 查询用户历史最近的10条健康数据
        $healthList = UserHealth::where("user_id", $user->user_id)->limit(10)->order("create_time","desc")->select();
        // 体温
        $temperature = array();
        // 血压
        $blood = array();
        // 心率
        $heart = array();
        // 时间
        $date = array();
        foreach ($healthList as $key => $value) {
            $temperature[] = $value->temperature;
            $blood[] = $value->blood_pressure;
            $heart[] = $value->heart_rate;
            $date[] = $value->create_time;
        }
        $health = [
            "grid" => [
                "bottom" => "3%",
                "containLabel" => true,
                "left" => "3%",
                "right" => "4%"
            ],
            "legend" => ["体温",  "血压", "心率"],
            "series" => [
                [
                    "areaStyle" => ["normal" => ""],
                    "data" => $temperature,
                    "name" => "体温",
                    "stack" => "温度",
                    "type" => "line"
                ],
                [
                    "areaStyle" => ["normal" => ""],
                    "data" => $blood,
                    "name" => "血压",
                    "stack" => "温度",
                    "type" => "line"
                ],
                [
                    "areaStyle" => ["normal" => ""],
                    "data" => $heart,
                    "name" => "心率",
                    "stack" => "次数",
                    "type" => "line"
                ],
            ],
            "title" => "健康数据",
            "tooltip" => [
                "axisPointer" => [
                    "label" => ["backgroundColor" => "#E9EEF3"],
                    "type" =>"cross"
                ],
                "trigger" => "axis"
            ],
            "xAxis" => [
                "boundaryGap" => false,
                "data" => $date
            ],
            "yAxis" => ["type" => "value"]
        ];

        return json($health);
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
            return jsonAPI("创建成功！", 200);
        }else{
            return jsonAPI("创建失败！", 400);
        }
    }
}