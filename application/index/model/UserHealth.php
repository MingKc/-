<?php
namespace app\index\model;
use think\Model;

class UserHealth extends Model{
    // 数据表名
    protected $table = "user_health";
    // 自动写入插入时间戳
    protected $insert = ["create_time"];

    public function setCreateTimeAttr(){
        return time();
    }

    // 返回历史健康数据
    public function returnHealth($healthList){
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
        return $health;
    }
}