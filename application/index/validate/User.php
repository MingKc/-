<?php
namespace app\index\validate;
use think\Validate;

class User extends Validate{

	//提交验证
	protected $rule=[
		"username"=>"require|min:6|max:12",
		"password"=>"require|min:6|max:18"
	];

	//反馈信息
	protected $message=[
		"username.require"=>"用户名不能为空",
		"username.min"=>"用户名长度不能少于6位",
		"username.max"=>"用户名长度不能大于12位",
		"password.require"=>"密码长度不能为空",
		"password.min"=>"密码长度不能低于6位",
		"password.max"=>"密码长度不能大于18位",
	];
}