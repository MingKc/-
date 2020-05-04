<?php
namespace app\index\controller;
use app\tools\AdminController;
use app\index\model\UserHealth;
use app\tools\UserToken;

class Health extends AdminController{
    // 查询历史健康数据
    public function query(){
        $usertoken = new UserToken();
        $user_id = $usertoken->checkToken();
        // 查询用户历史最近的10条健康数据
        $healthList = UserHealth::where("user_id", $user_id)->limit(10)->order("create_time","desc")->select();
        // 体温
        $temperature = array();
        // 高压
        $highBlood = array();
        // 低压
        $lowBlood = array();
        // 心率
        $heart = array();
        // 时间
        $date = array();
        foreach ($healthList as $key => $value) {
            $temperature[] = $value->temperature;
            $highBlood[] = $value->high_pressure;
            $lowBlood[] = $value->low_pressure;
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
            "legend" => [
                        "data" => ["体温",  "高压",  "低压","心率"]
                    ],
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
                    "data" => $highBlood,
                    "name" => "高压",
                    "stack" => "mmHg",
                    "type" => "line"
                ],
                [
                    "areaStyle" => ["normal" => ""],
                    "data" => $lowBlood,
                    "name" => "低压",
                    "stack" => "mmHg",
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
            "title" => [
                "text" => "健康数据"
            ],
            "tooltip" => [
                "axisPointer" => [
                    "label" => ["backgroundColor" => "#E9EEF3"],
                    "type" =>"cross"
                ],
                "trigger" => "axis"
            ],
            "xAxis" => [ 
                "data" => $date,
            ],
            "yAxis" => ["type" => "value"]
        ];
        return jsonAPI("查询成功！", 200, $health);
    }

    // 插入健康数据
    public function add(){
        $data = processRequest();
        $usertoken = new UserToken();
        if(!isset($data["temperature"]) || !isset($data["highBlood"]) || !isset($data["lowBlood"]) || !isset($data["heart"])){
            return jsonAPI("请求参数为空！", 400);
        }
        $user = new UserHealth([
            "temperature" => $data["temperature"],
            "high_pressure" => $data["highBlood"],
            "low_pressure" => $data["lowBlood"],
            "heart_rate" => $data["heart"],
            "user_id" => $usertoken->checkToken()
        ]);
        if($user->save()){
            $info = [
                "temperature" => $data["temperature"],
                "highBlood" => $data["highBlood"],
                "lowBlood" => $data["lowBlood"],
                "heart" => $data["heart"],
                "create_time" => $user->create_time,
                "health_id" => $user->id
            ];
            return jsonAPI("添加成功", 201, $info);
        }else{
            return jsonAPI("添加失败", 500);
        }
    }

    // 修改健康数据
    public function modify(){
        $usertoken = new UserToken();
        $user_id = $usertoken->checkToken();
        $data = processRequest();
        if(!isset($data["temperature"]) || !isset($data["highBlood"]) || !isset($data["lowBlood"]) || !isset($data["heart"]) || !isset($data["health_id"])){
            return jsonAPI("请求参数为空！", 400);
        }
        $user = UserHealth::where([
                "user_id" => $user_id,
                "id" => $data["health_id"]
            ])->find();
        $user->temperature = $data["temperature"];
        $user->high_pressure = $data["highBlood"];
        $user->low_pressure = $data["lowBlood"];
        $user->heart_rate = $data["heart"];
        if($user->save()){
            $info = [
                "temperature" => $data["temperature"],
                "highBlood" => $data["highBlood"],
                "lowBlood" => $data["lowBlood"],
                "heart" => $data["heart"],
                "create_time" => $user->create_time,
                "health_id" => $user->id
            ];
            return jsonAPI("修改成功!", 200, $info);
        }else{
            return jsonAPI("修改失败!", 500);
        }
    }

    // 健康评估
    public function estimate(){
        // 获取该用户近7天体温，血压，心率的平均值
        // 体温范围：36-37.5
        // 高压范围：90-140
        // 低压范围：60-90
        // 心率：60-100
        $usertoken = new UserToken();
        $user_id = $usertoken->checkToken();
        $avgTemperature = UserHealth::where("user_id", $user_id)->limit(7)->order("create_time", "desc")->avg("temperature");
        $avgHighBlood = UserHealth::where("user_id", $user_id)->limit(7)->order("create_time", "desc")->avg("high_pressure");
        $avgLowBlood = UserHealth::where("user_id", $user_id)->limit(7)->order("create_time", "desc")->avg("low_pressure");
        $avgHeart = UserHealth::where("user_id", $user_id)->limit(7)->order("create_time", "desc")->avg("heart_rate");
        // 体温偏高 -10
        // 体温偏低 -10
        // 血压偏高 -20
        // 血压偏低 -15
        // 心率过快 -8
        // 心率过慢 -10
        return dump($level);
    }
}