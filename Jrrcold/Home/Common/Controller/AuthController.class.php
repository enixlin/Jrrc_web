<?php

namespace Common\Controller;

use Think\Controller;
use Think\Auth;

class AuthController extends Controller {
	protected function _initialize(){
		$auth=new Auth();
		
		if(!$auth->check()){
			$this->error('no auth');
		}
	}
}