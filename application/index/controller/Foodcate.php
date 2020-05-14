<?php
namespace app\index\controller;
use app\tools\AdminController;
use app\index\model\FoodCate as FoodCateModel;

class Foodcate extends AdminController{
    public function getlist(){
        $cate = new FoodCateModel();
        $list = $cate->select();
        if($list){
            return jsonAPI("查询成功!", 200, $list);
        }
        return jsonAPI("查询失败!", 500);
    }
}