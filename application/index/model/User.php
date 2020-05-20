<?php
namespace app\index\model;
use think\Model;
use traits\model\SoftDelete;
use app\index\model\Role;

class User extends Model{
    // 软删除
    use SoftDelete;
    // 主键
    protected $pk="user_id";
    // 自动写入时间戳和IP
    protected static $deleteTime=["delete_time"];
    protected $insert = ["create_time"];
    protected $update = ["update_time"];
    protected $auto=["ip"];

    public function setCreateTimeAttr(){
        return time();
    }

    public function setUpdateTimeAttr(){
        return time();
    }
    
    protected function setIpAttr(){
        return request()->ip();
    }

    public function getList($list){
        $user = array();
        foreach ($list as $key => $value) {
            $role = Role::where("role_id", $value->role_id)->find();
            $user[] = [
                    "id" => $value->user_id,
                    "username" => $value->username,
                    "create_time" => $value->create_time,
                    "status" => $value->status === 1,
                    "role_name" => $role->role_name
                ];
        }
        return $user;
    }
}