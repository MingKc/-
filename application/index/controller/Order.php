<?php
namespace app\index\controller;
use app\tools\AdminController;
use app\tools\UserToken;
use app\index\model\Order as OrderModel;
use app\index\model\OrderFoods;
use app\index\model\Food;

class Order extends AdminController{
    //订单查询
    public function orders(){
        $userToken = new UserToken();
        //获取用户id
        $user_id = $userToken->checkToken();
        $orderLists = OrderModel::where("user_id", $user_id)->select();
        $order = array();
        foreach ($orderLists as $key => $value) {
            $order[] = [
                "order_id" => $value->order_id,
                "order_price" => $value->order_price,
                "pay_status" => $value->pay_status,
                "create_time" => $value->create_time
            ];
        }
        return jsonAPI("查询成功！", 200, $order);
    }

    //查看订单详情
    public function desc(){
        $data = processRequest();
        if(!isset($data["order_id"])){
            return jsonAPI("查询参数为空！", 401);
        }
        // 获取订单id
        $order_id = $data["order_id"];
        $userToken = new UserToken();
        //获取用户id
        $user_id = $userToken->checkToken();
        $orders = OrderFoods::where(["order_id" => $order_id, "user_id" => $user_id])->select();
        $desc = array();
        foreach ($orders as $key => $value) {
            $food = Food::where("food_id", $value->food_id)->find();
            $desc[] = [
                "food_name" => $food->food_name,
                "food_price" => $food->food_price,
                "food_number" => $value->food_number
            ];
        }
        return jsonAPI("查询成功！", 200, $desc);
    }

    //删除订单
    public function delete(){
        $data = processRequest();
        if(!isset($data["order_id"])){
            return jsonAPI("查询参数为空！", 401);
        }
        // 获取订单id
        $order_id = $data["order_id"];
        $userToken = new UserToken();
        //获取用户id
        $user_id = $userToken->checkToken();
        $orderFoods = new OrderModel();
        $orders = $orderFoods->where(["order_id" => $order_id, "user_id" => $user_id])->delete();
        if($orders){
            return jsonAPI("删除订单成功！", 204);
        }else{
            return jsonAPI("删除订单失败！", 500);
        }
    }
}