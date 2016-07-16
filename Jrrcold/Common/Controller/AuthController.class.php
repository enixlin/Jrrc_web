<?php
namespace Common\Controller;

use Think\Controller;
use Think\Auth;

class AuthController extends Controller  {
	protected function _initialize(){
		$Auth=new Auth();
		$session_auth=session('name');
		if(!$Auth->check(MODULE_NAME."/".CONTROLLER_NAME."/".ACTION_NAME ,session('uid'))){
			$this->error('没权限使用！返回',"",1);
		}
	}
}