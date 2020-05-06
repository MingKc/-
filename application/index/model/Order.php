<?php
namespace app\index\model;
use think\Model;
use traits\model\SoftDelete;

class Order extends Model{
    // 软删除
    use SoftDelete;
    // 自动写入时间戳
    protected $insert = ["create_time"];
    // 软删除标记字段
    protected static $deleteTime=["delete_time"];

    public function setCreateTimeAttr(){
        return time();
    }
}