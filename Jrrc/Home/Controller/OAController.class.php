<?php

namespace Home\Controller;

use Common\Controller\AuthController;

class OAController extends AuthController {
		public function index(){	
			$this->display('index');		
		}

		public function login(){
			$oa_name=session('oa_name');
			$oa_password=session('oa_password');
			if($oa_name==''){
				echo "你还没有设置统一登录平台密码！请点击左则[保存平台密码]设置";
				return ;
			}
			$this->assign('oa_name',$oa_name);
			$this->assign('oa_password',$oa_password);
			$this->display('login');
		}
		
		public function setup(){
			$this->display('setup');
		}
		
		
		public function updatesetup(){
			$model=M('user');
			$date['oa_name']=I('post.oaname');
			$date['name']=session('name');
			$date['id']=session('uid');
			$date['oa_password']=I('post.oapassword');
			$result=$model->save($date);
			if($result!=0){
				echo $result;
				session('oa_name',I('post.oaname'));
				session('oa_password',I('post.oapassword'));
			}else{
				echo $result;
			}
			
		}
		
}