<?php

namespace Admin\Controller;

use Think\Controller;

class LoginController extends Controller {
	
	public function Index(){
		$this->display('index');
	}
	
	// 登录状态校验
	public function checkLogin() {
		if (session ( 'id' ) == null) {
			$this->error ( "你还没有登录，不能操作", "/Jrrc_web/Admin/" );
		}
	}
	
	// 用户登录
	public function login() {
		$User = M ( "User" );
		$data ["name"] = I ( "post.name" );
		$data ["password"] = I ( "post.password" );
		$data = $User->create ( $data );
		$result = $User->where ( $data )->select ();
		if ($result == null) {
			$this->error ( "false" );
		} else {
			session_start ();
			session ( "name", $result [0] ["name"] );
			session ( "id", $result [0] ["id"] );
			session ( "power", 'user' );
			$this->success ( 'true' );
		}
	}
	
	// 游客身份登录
	public function guestLogin() {
		session ( 'name', 'guest' );
		session ( 'power', 'guest' );
		$this->success ( '游客登录成功', '/Jrrc_web/Admin/Main/', 0 );
	}
	
	// 用户退出
	public function logout() {
		session ( null );
		$this->success ( '退出成功', '/Jrrc_web/Admin/Index' );
	}
	
}