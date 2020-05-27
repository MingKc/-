<?php
namespace app\index\controller;
use app\tools\AdminController;
use app\tools\UserToken;
use app\index\model\Order as OrderModel;
use app\index\model\OrderFoods;
use app\index\model\Food;
use think\Db;

class Order extends AdminController{
    //订单查询
    public function orders(){
        $data = processRequest();
        // 获取当前页码和每页条数
        if(!isset($data["pagenum"]) || !isset($data["pagesize"])){
            return jsonAPI("查询参数为空!", 400);
        }
        $pagenum = $data["pagenum"];
        $pagesize = $data["pagesize"];
        $userToken = new UserToken();
        //获取用户id
        $user_id = $userToken->checkToken();
        $total = OrderModel::where("user_id", $user_id)->count();
        $orderLists = OrderModel::where("user_id", $user_id)->order("create_time", "desc")->page($pagenum, $pagesize)->select();
        $order = array();
        foreach ($orderLists as $key => $value) {
            $order[] = [
                "order_id" => $value->order_id,
                "order_price" => $value->order_price,
                "pay_status" => $value->pay_status,
                "create_time" => $value->create_time
            ];
        }
        $date = [
            "total" => $total,
            "pagenum" => $pagenum,
            "order" => $order
        ];
        return jsonAPI("查询成功！", 200, $date);
    }

    //查看订单详情
    public function desc(){
        $data = processRequest();
        if(!isset($data["order_id"])){
            return jsonAPI("查询参数为空！", 401);
        }
        // 获取订单id
        $order_id = $data["order_id"];
        $orders = OrderFoods::where("order_id", $order_id)->select();
        $desc = array();
        foreach ($orders as $key => $value) {
            $food = Food::where("food_id", $value->food_id)->find();
            $desc[] = [
                "food_name" => $food->food_name,
                "food_price" => $food->food_price,
                "food_total_price" => $value->food_total_price,
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
        $orderModel = new OrderModel();
        $orders = $orderModel->where(["order_id" => $order_id, "user_id" => $user_id])->find();
        if(!$orders){
            return jsonAPI("删除订单失败！", 500);
        }
        if($orders->delete()){
            return jsonAPI("删除订单成功！", 200);
        }
        return jsonAPI("删除订单失败！", 500);
    }

    //提交订单
    public function add(){
        $data = processRequest();
        $userToken = new UserToken();
        $user_id = $userToken->checkToken();
        if(!isset($data["order_price"]) || !isset($data["order_desc"])){
            return jsonAPI("提交参数为空！", 401);
        }
        // 转成数组对象
        $order_desc = json_decode($data["order_desc"]);
        // 启动事务
        Db::startTrans();
        try{
            // 保存订单到order表中
            $order = new OrderModel(["user_id" => $user_id, "order_price" => $data["order_price"]]);
            $order->save();
            $order_id = $order->order_id;
            $list = array();
            for ($i=0; $i < count($order_desc); $i++) {
                $list[] = [
                    "order_id" => $order_id,
                    "food_id" => $order_desc[$i]->id,
                    "food_number" => $order_desc[$i]->number,
                    "food_total_price" => $order_desc[$i]->total_price
                ]; 
            }
            $order_foods = new OrderFoods();
            $order_foods->saveAll($list);
            // 提交事务
            Db::commit();
            return jsonAPI("订单提交成功！", 200);
        }catch(Exception $e){
            // 回滚
            Db::rollback();
        }
        return jsonAPI("订单提交失败！", 500);
    }
}