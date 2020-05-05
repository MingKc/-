<?php
namespace app\index\model;
use think\Model;

class Order extends Model{
    // 自动写入时间戳
    protected $insert = ["create_time"];
}