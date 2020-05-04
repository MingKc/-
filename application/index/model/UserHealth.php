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
}