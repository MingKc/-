<?php
namespace app\index\model;
use think\Model;
use app\index\model\Auth;

class Role extends Model{
    public function saveAuth($role_auth_ids){
        $auth_id = explode(",", $role_auth_ids);
        $auth = Auth::select($auth_id);
        $s = "";
        foreach ($auth as $key => $value) {
            if(!empty($value['auth_controller']) && !empty($value['auth_action'])){
                $s .= $value['auth_controller']."/".$value['auth_action'].",";
            }
        }
        // 删除末尾，
        $s = rtrim($s,',');
        return $s;
    }
}