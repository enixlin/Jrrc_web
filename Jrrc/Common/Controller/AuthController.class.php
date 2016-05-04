<?php

namespace Common\Controller;

use Think\Auth;
use Think\Controller;

class AuthController extends Controller {
	protected function _initialize() {
		$auth = new Auth ();
		// 请注意，CHECK函数的第一个参数为有权限的模块、控制器和方法，分别与auth_rule表的name字段对应
		// 如果name字段只有模块和控制器，那些参数就不能写上方法名，否则验证会失败！
		if (! $auth->check ( MODULE_NAME . "/" . CONTROLLER_NAME . "/", session ( 'uid' ) ))
			$this->error ( '没有权限使用该功能，返回主页', "/Jrrc_web/Home/index" );
	}
}

