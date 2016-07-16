<?php

namespace Home\Controller;

use Think\Controller;

class MainController extends Controller {
	public function Index() {
		$this->assign ( 'name', session ( 'name' ) );
		$this->initial ( session ( 'power' ) );
		$this->display ( 'main' );
	}
	/*
	 * 主功能页面，按用户权限显示功能菜单
	 */
	public function initial($power) {
		if ($power == 'user') {
		
		}
	}
	
	/*
	 * 取得用户所有组的所有权限功能
	 *
	 */
	public function getFunction($power) {
	}
}