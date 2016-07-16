<?php

namespace Admin\Controller;

use Think\Controller;
use Admin\Model\UserModel;
use Admin\Model\UserRelationModel;

class UserController extends Controller {
	public function index() {
		// $User =new UserRelationModel('User') ;
		$User = D ( "User" );
		dump ( $User->relation ( TRUE )->select () );
	}
}
