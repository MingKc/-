<?php
namespace app\index\model;
use think\Model;

class Food extends Model{
    
    // food_name字段搜索器
    public function getList($foodList){
        $food = array();
        foreach ($foodList as $key => $value) {
            $food[] = [
                    "food_name" => $value->food_name,
                    "fodd_price" => $value->food_price
                ];
        }
        return $food;
    }
}