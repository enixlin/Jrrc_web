<?php

namespace Admin\Controller;

use Think\Controller;
use Admin\Model\UserRoleModel;
use Admin\Model\UserRelationModel;

class MainController extends Controller {
	public function Index() {
		$this->checkLogin ();
		if ((session ( "name" ) != null)) {
			$this->success ( '你已登录', '/Jrrc_web/Admin/Index/main', 0 );
		}
		$this->display ( 'index' );
	}
	
	// 用户管理
	public function UserList() {
		$this->checkLogin ();
		$User =D('User');
		$list = $User->relation ( true )->select ();
		$this->assign ( "list", $list );
		// dump($list);
		$this->display ( 'UserList' );
	}
	// 显示增加用户页面
	public function showAddUser() {
		// 取得所有角式
		$Role = M ( 'auth_group' );
		$result = $Role->select ();
		$this->assign ( 'role', $result );
		$this->display ( "addUser" );
	}
	
	// 增加用户处理
	public function addUser() {
		if(I('name'=='')){
			exit($this->error('用户名不能为空'));
		}
		if(I('password'=='')){
			exit($this->error('密码不能为空'));
		}
		
		$User = M( "User" );
		$data ['name'] = I ( 'name' );
		$data ['password'] = I ( 'password' );
		$data ['status'] = I ( 'status' );
		$user_role = M ( "role_user" );
		//验证输入USER表的字段，
		if (! $User->create ( $data )) {
			exit ( $User->getError () );
		} else {
			$uid = $User->add ( $data );
			//增加用户成功后，关联用户角色
			if ($uid) {
				$role ['user_id'] = $uid;
				$role ['role_id'] = I ( 'role' );
				$rid=$user_role->add ( $role );
				if($rid){
					$this->success ( 'ok' );
				}else{
					// 创建用户角色失败后，自动删除用户
					$User->where('id='.$uid)->delete();
					$this->error ( '创建用户角色失败，已删除用户' );
				}
				
			} else {
				$this->error ( '创建用户失败' );
				
			}
		}
	}
	// 删除用户
	public function deleteUser($id) {
		$this->checkLogin ();
// 		if (session ( 'power' ) != 'admin') {
// 			$this->error ( '你不是管理员，无权删除用户' );
// 		}
		$User = D ( "User" );
		$data ['id'] = I ( "get.id" );
		if ($User->create ( $data )) {
			$User->relation(true)->where ( $data )->delete ();
			$this->success ('','/jrrc_web/admin/Rbac/userList/');
		} else {
			$this->error ();
		}
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
			$this->error ( "你还没有登录，不能操作", "/Jrrc_web/Admin/" );
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
	
	// 取得所有的用户
	public function getAllUser() {
		$User = D ( 'User' );
		$result = $User->select ();
		$name = array ();
		foreach ( $result as $v ) {
			array_push ( $name, $v ["name"] );
		}
		echo json_encode ( $name );
	}
}