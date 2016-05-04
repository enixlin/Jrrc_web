<?php
namespace Admin\Controller;
use Common\Controller\AuthController;

class IndexController extends AuthController {
	//判断登录的用户所属的组，如果是管理员组的，直接进入后台管理页面
	public function Index() {
	if(session('auth')){
	if ($session('auth')=='管理员组'){
			$this->redirect('Jrrc_web/jrrcold/Admin/main');
		}
		else{
			$this->redirect('/Jrrc_web/Admin/Index/adminLogin');
		}
	}
		
	}
	

	

}