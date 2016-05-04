<?php

namespace Home\Controller;

use Think\Controller;
use Think\Model;

class LoginController extends Controller {
	public function Index() {
		// echo 'login controller';
		$this->display ( 'Index' );
	}
	
	/* */
	/*
	 * 获取所有用户
	 */
	/* */
	public function getAllUser() {
		$User = M ( 'User' );
		$name = $User->field ( "name" )->select ();
		// dump($name);
		echo json_encode ( $name );
	}
	
	/* */
	/*
	 * // 用户登入
	 */
	/* */
	public function login() {
		$User = M ( 'User' );
		// 读取所有用户
		$result = $User->select ();
		// 设定匹配标记
		$matchFlag = 'False';
		$uid='';
		
		foreach ( $result as $v ) {
			if ($v ['name'] == I ( 'post.name' ) && $v ['password'] == I ( 'post.password' )) {
				// 用户名和密码相符
				$matchFlag = 'true';
				$uid=$v[id];
			}
		}
		if ($matchFlag == 'true') {
			session_start ();
			SESSION ( 'name', I ( 'post.name' ) );
			session('uid',$uid);
			$auth=M('auth_group_access');
		
			echo "True";
		} else {
			echo "False";
		}
	}
	
	/* */
	/*
	 * // 游客登入
	 */
	/* */
	public function guestLogin(){
		session('name','guest');
		session('power','admin');
		redirect("/Jrrc_web/Home/Main/");
	}
	
	
	/* */
	/*
	 * // 用户登出
	 */
	/* */
	public function Logout() {
		session ( null );
		$this->success ( '退出成功', '/Jrrc_web/' );
	}
}