<?php
namespace app\index\model;
use think\Model;
use traits\model\SoftDelete;

class User extends Model{
    use SoftDelete;
    // 主键
    protected $pk="user_id";
    protected static $deleteTime="delete_time";
    protected $auto=["ip"];
    protected function setIpAttr(){
        return request()->ip();
    }
}