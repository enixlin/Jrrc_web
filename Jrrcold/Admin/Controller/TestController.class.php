<?php

namespace Admin\Controller;
//namespace Think;

// use Common\AuthController;
use Think\Controller;
use Common\Controller\AuthController;
class TestController extends AuthController{
	public function index(){
		echo "auth page";
	}
}