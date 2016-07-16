<?php
namespace Home\Controller;

use Think\Controller;

class LoginController extends Controller {
	public function index() {
		if(strpos($_SERVER['HTTP_USER_AGENT'],'MSIE 6.0')){
			echo "浏览器版本为旧式的IE6，请<a href='/Jrrc_web/Public/ie8.exe'>下载IE8浏览器</a>升级或使用360浏览器";
			exit();
		}
		//echo session ( 'name' );
		if (session ( 'name' ) != null) {
			$this->redirect ( '/Home/Main/Index' );
		} else {
			$this->display ( 'index' );
		}
	}
	
	// 取得所有的用户名
	public function getAllUser() {
		$User = M ( 'User' );
		$UserList = $User->field ( 'name' )->select ();
		echo json_encode ( $UserList );
	}
	
	// 用户登录处理
	public function login() {
		$User = D ( 'User' );
		$Userlist = $User->relation ( true )->select ();
		$flag = 'false';
		foreach ( $Userlist as $v ) {
			if ($v ['name'] == I ( 'name' ) && $v ['password'] == I ( 'password' )) {
				// 保存用户名到session
				session ( 'name', I ( 'name' ) );
				session('uid',$v['id']);
				session('oa_name',$v['oa_name']);
				session('oa_password',$v['oa_password']);
				// 保存用权限到session
				//session ( 'auth', $v ['auth_group'] [0] ['title'] );
				
				$flag = 'true';
			}
		}
		
		if ($flag == 'true') {
			echo 'true';
		} else {
			echo 'false';
		}
	}
	
	
	// 用户登出处理
	public function logout(){
		session(null);
		$this->success('退出成功',"/Jrrc_web/Home/Index");
	}
	public  function changepw(){
		$model=M('user');
		$userlist=$model->select();
		$this->assign('userlist',$userlist);
		$this->display('changepw');
	
	}
	
	//修改密码
	public  function handlechangepw($id,$oldpw,$newpw,$confirmnewpw){

		$model=M('user');
		$result=$model->where('id='. $id)->select();
		if($result[0][password]!=$oldpw){
			echo "旧密码不正确";
			return;
		}

		$data['id']=$id;
		$data['password']=$newpw;
		$result=$model->save($data);
		echo $result;	
	}
}