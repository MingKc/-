<?php
namespace app\index\controller;
use app\tools\AdminController;
use app\index\model\Food as FoodModel;

class Food extends AdminController{
    // 查询菜品分类查询菜单
    public function list(){
        $data = processRequest();
        // 获取食品分类id
        if(!isset($data["cate"])){
            return jsonAPI("查询参数为空!", 400);
        }
        $cate = $data["cate"];
        $foodModel = new FoodModel();
        $cateList = $foodModel->where("food_cate", $cate)->select();
        $food = $foodModel->getList($cateList);
        return jsonAPI("查询成功！", 200, $food);
    }

    //根据名称查询菜品
    public function query(){
        $data = processRequest();
        //获取菜品名称
        if(!isset($data["food_name"])){
            return jsonAPI("查询参数为空!", 400);
        }
        $name = $data["food_name"];
        $foodModel = new FoodModel();
        $foodList = $foodModel->where("food_name", "like", "%".$name)->select();
        $food = $foodModel->getList($foodList);
        return jsonAPI("查询成功！", 200, $food);
    }
}