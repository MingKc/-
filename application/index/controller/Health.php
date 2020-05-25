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
        $user = new UserHealth();
        $healthList = $user->where("user_id", $user_id)->limit(10)->order("create_time","desc")->select();
        $health = $user->returnHealth($healthList);
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
                "health_id" => $user->id
            ];
            return jsonAPI("添加成功", 200, $info);
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
                "health_id" => $user->id
            ];
            return jsonAPI("修改成功!", 200, $info);
        }else{
            return jsonAPI("修改失败!", 500);
        }
    }

    // 获取当前健康数据
    public function today(){
        $usertoken = new UserToken();
        $user_id = $usertoken->checkToken();
        // 查询用户历史最近健康数据
        $user = new UserHealth();
        $healthList = $user->where("user_id", $user_id)->order("create_time","desc")->find();
        $last_time = explode(" ", $healthList->create_time)[0];
        $now_time = explode(" ", date("yy-m-d H:i:s"))[0];
        if($last_time === $now_time){
            $data = [
                "health_id" => $healthList->id,
                "temperature" => $healthList->temperature,
                "highBlood" => $healthList->high_pressure,
                "lowBlood" => $healthList->low_pressure,
                "heart" => $healthList->heart_rate
            ];
            return jsonAPI("查询成功！", 200, $data);
        }
        return jsonAPI("当天未录入数据！", 202);
    }
}