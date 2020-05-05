<?php
namespace app\index\model;
use think\Model;

class UserEstimate extends Model{
    protected $table = "user_health_estimate";


    public function saveSuggest($user_id, $type, $grade){
        // 找到对应建议的所有id集
        $suggest_ids = db("suggest")->where("suggest_type", $type)->select();
        $suggest = "";
        $suggestions = array();
        foreach ($suggest_ids as $key => $value) {
            $suggest = $suggest.",".$value["suggest_id"];
            $suggestions[] = [ "suggest" => $value["suggest_desc"], "type" => $value["suggest_sort"] ];
        }
        $suggest = substr($suggest, 1, strlen($suggest));
        $user = db("user_health_estimate")->where("user_id", $user_id)
                ->update([
                        "health_suggest" => $suggest,
                        "health_grade" => $grade
                    ]);
        $info = [
            "suggest" => $suggestions,
            "grade" => $grade
        ];
        return $info;
    }
}