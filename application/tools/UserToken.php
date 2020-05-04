<?php
namespace app\tools;
use app\index\model\User;

class UserToken{
    // 生成token
    public function getToken($user){
        $userinfo = $user->username.$user->password;
        // 存储token
        $token = md5($userinfo.sha1(substr(time(),2,5)))."_".time();
        $user->token = $token;
        $user->save();
        return $token;
    }

    // 验证token
    public function checkToken($token){
        $user = User::where("token", $token)->find();
        if($user != null){
            $time = explode("_", $user->token);
            // token有效时间为16分钟
            if(time() - $time[1] > 1000){
                // token过期
                return false;
            }else{
                // 验证通过返回用户id
                return $user->user_id; 
            }
        }else{
            return false;
        }
    }
}
