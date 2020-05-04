<?php
namespace app\tools;
use think\Controller;
use app\index\model\User;
use app\index\model\Role;
use app\tools\UserToken;


// app普通控制器的父类控制器
class AdminController extends Controller{

	public function __construct(){
		// 避免覆盖Controller方法
		parent::__construct();

		// 当前请求
		$controller=request()->controller();
		$action=request()->action();
		$now_action=$controller."/".$action;

		// 允许访问控制器方法
		$allow="User/register,User/login";
		$usertoken = new UserToken();
		$token = request()->header("token");
		if(strpos($allow, $now_action) === false){
			if($token == ""||!$usertoken->checkToken($token)){
				echo jsonAPI("无效token!", 401);
				exit;
			}else{
				// 检查权限
				$user = new User();
				$role = new Role();
				// 根据id查找用户信息中的角色id
				$user_id = $usertoken->checkToken($token);
				$userinfo = $user->find($user_id);
				$role_id = $userinfo["role_id"];
				// 根据角色id查看拥有权限
				$role_info = $role->find($role_id);
				$auth = $role_info["role_auth_ac"];
				echo dump($role_info);
				die;
				if(strpos($auth, $now_action) === false && strpos($allow, $now_action) === false){
					// 没有访问权限
					echo jsonAPI("没有访问权限!", 403);
					exit;
				}
			}
		}

		// echo dump($token->checkToken(["username"=>"122","password"=>"123"]));
		
		// echo dump();
		// $s=new Token();
		// echo dump($s->check(request()->header("token")));
		
		// echo dump(request()->has("Authorization",$data));

		
	}

}
