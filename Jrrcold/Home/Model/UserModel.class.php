<?php

namespace Home\Model;

use Think\Model;

class UserModel extends Model {

	protected $_validate = array (//
			// 用户名必须填写
			array("name",'require','用户名必须填写',3,'regex'),//
			array("name",'','用户已存在',0,'unique',1),//
			array("password",'require','密码必须填写',3,'regex'),//

	);
	// 用户名不能有特殊字符
}

	
