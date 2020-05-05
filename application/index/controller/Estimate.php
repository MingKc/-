<?php
namespace app\index\controller;
use app\tools\AdminController;
use app\tools\UserToken;
use app\index\model\UserHealth;
use app\index\model\UserEstimate;

class Estimate extends AdminController{
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
        // sort 0为通用，1为血压，2为体温，3为心率 4去医院
        // grade 高血压2.5 发烧2.0 高血压+发烧0.5 心率过快3.5 健康4.0
        $userEstimate = new UserEstimate();
        if($avgTemperature > 37.5 && $avgHighBlood > 140){
            $user = $userEstimate->saveSuggest($user_id, 4, 0.5);
        }else if($avgTemperature>37.5){
            $user = $userEstimate->saveSuggest($user_id, 2, 2.0);
        }else if($avgHighBlood>140){
            $user = $userEstimate->saveSuggest($user_id, 1, 2.5);
        }else if($avgHeart){
            $user = $userEstimate->saveSuggest($user_id, 3, 3.5);
        }else{
            $user = $userEstimate->saveSuggest($user_id, 0, 4.0);
        }
        // if(!$user){
        //     return jsonAPI("查询健康评估失败！", 500);
        // }
        return jsonAPI("查询健康评估成功！", 200, $user);
    }
}