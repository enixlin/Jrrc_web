<?php

namespace Home\Controller;

use Think\Controller;

class UserController extends Controller {
	public function Index() {
		if ( ( session ( "name" )!=null )) {
			$this->success ('你已登录', '/Jrrc_web/Home/main/',0 );
		}
		$this->display ( 'index' );
	}
	
	// 增加用户
	public function addUser($name, $password) {
		$User = D ( "User" );
		$data ['name'] = $name;
		$data ['password'] = $password;
		if (! $User->create ( $data )) {
			echo $User->getError ();
		} else {
			$User->add ( $data );
			$this->success ( "添加用户成功", "/Jrrc_web/" );
		}
	}
	// 删除用户
	public function deleteUser($id) {
		$this->checkLogin ();
		$User = M ( "User" );
		$data ['id'] = session ( "id" );
		$data = $User->create ( $data );
		$User->where ( $data )->delete ();
	}
	
	//
	// 修改密码
	public function modifyPassword($id, $password) {
		$this->checkLogin ();
		$User = M ( "User" );
		$data ['password'] = $password;
		$data ['id'] = $id;
		$data = $User->create ( $data );
		$User->save ();
	}
	
	// 登录状态校验
	public function checkLogin() {
		if (session ( 'id' ) == null) {
			$this->error ( "你还没有登录，不能操作", "/Jrrc_web/" );
		}
	}
	
	// 检验用户名是否重复
	public function duplicationName($name) {
		$User = M ( "User" );
		$data ["name"] = $name;
		$data = $User->create ( $data );
		$result = $User->where ( $data )->select ();
		$flag = 0;
		foreach ( $result as $v ) {
			if ($v ["name"] == $name) {
				$flag = 1;
			}
		}
		
		if ($flag == 1) {
			return 1;
		} else {
			return 0;
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
			session("power",'user');
			$this->success ( 'true' );
		}
		// dump ( $result [0] ["name"] );
	}
	
	
	//游客身份登录
	public function guestLogin(){
		session('name','guest');
		session('power','guest');
		$this->success('游客登录成功','/Jrrc_web/Home/Main/',0);
	}
	
	// 用户退出
	public function logout() {
		session ( null );
		$this->success ( '退出成功', 'Index' );
	}
	
	// 取得所有的用户
	public function getAllUser() {
		$User = M ( 'User' );
		$result = $User->select ();
		$name = array ();
		foreach ( $result as $v ) {
			array_push ( $name, $v ["name"] );
		}
		echo json_encode ( $name );
	}
}