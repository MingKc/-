<?php
namespace app\index\controller;
use app\tools\AdminController;
use app\index\model\Food as FoodModel;

class Food extends AdminController{
    // 查询菜品分类查询菜单
    public function list(){
        $data = processRequest();
        // 获取当前页码和每页条数
        if(!isset($data["pagenum"]) || !isset($data["pagesize"])){
            return jsonAPI("查询参数为空!", 400);
        }
        $pagenum = $data["pagenum"];
        $pagesize = $data["pagesize"];
        $foodModel = new FoodModel();
        
        if(isset($data["cate"])){
            // 根据商品分类查询
            $cate = $data["cate"];
            $total  = $foodModel->where("food_cate", $cate)->count();
            $list = $foodModel->where("food_cate", $cate)->page($pagenum, $pagesize)->select();
        }else if(isset($data["food_name"])){
            //根据名称查询菜品
            $name = $data["food_name"];
            $total = $foodModel->where("food_name", "like", "%".$name."%")->count();
            $list = $foodList = $foodModel->where("food_name", "like", "%".$name."%")->page($pagenum, $pagesize)->select();
        }else{
            return jsonAPI("查询参数为空!", 400);
        }
        $food = $foodModel->getList($list);
        $data = [
            "total" => $total,
            "pagenum" => $pagenum,
            "food" => $food
        ];
        return jsonAPI("查询成功！", 200, $data);
    }

    //根据名称查询菜品
    // public function query(){
    //     $data = processRequest();
    //     //获取菜品名称
    //     if(!isset($data["food_name"])){
    //         return jsonAPI("查询参数为空!", 400);
    //     }
    //     $name = $data["food_name"];
    //     $foodModel = new FoodModel();
    //     $foodList = $foodModel->where("food_name", "like", "%".$name."%")->select();
    //     $food = $foodModel->getList($foodList);
    //     return jsonAPI("查询成功！", 200, $food);
    // }

    //菜品添加
    public function add(){
        $data = processRequest();
        //获取菜品名称
        if(!isset($data["food_name"]) || !isset($data["food_price"]) || !isset($data["food_cate"]) || !isset($data["food_pics"])){
            return jsonAPI("添加参数为空!", 400);
        }
        $food = new FoodModel([
                "food_name" => $data["food_name"],
                "food_price" => $data["food_price"],
                "food_cate" => $data["food_cate"],
                "food_pics" => $data["food_pics"]
            ]);
        if($food->save()){
            return jsonAPI("菜品添加成功！", 201);
        }else{
            return jsonAPI("菜品添加失败！", 500);
        }
    }

    //删除菜品
    public function delete(){
        $data = processRequest();
        // 获取删除菜品的id
        if(!isset($data["food_id"])){
            return jsonAPI("删除菜品的参数为空!", 400);
        }
        $food_id = $data["food_id"];
        $foodModel = new FoodModel();
        $food = $foodModel->where("food_id", $food_id)->delete();
        if($food){
            return jsonAPI("删除菜品成功！", 204);
        }else{
            return jsonAPI("删除菜品失败！", 500);
        }
    }
}