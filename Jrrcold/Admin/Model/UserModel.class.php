<?php

namespace Admin\Model;

use Think\Model\RelationModel;

class UserModel extends RelationModel {
	protected $_validate = array ( //
	                               // 用户名必须填写
			array (
					"name",
					'require',
					'用户名必须填写',
					3,
					'regex' 
			), //
			   // 是否启用必须选择',
			array (
					"status",
					'require',
					'是否启用必须选择',
					3,
					'regex' 
			), //
			array (
					"name",
					'',
					'用户已存在',
					0,
					'unique',
					1 
			), //
			array (
					"password",
					'require',
					'密码必须填写',
					3,
					'regex' 
			) 
	); //
	
	protected $_link = array (
			'Role' => array ( // 关联的名称
					'mapping_type' => self::MANY_TO_MANY,
					// 'class_name' => 'Group',
					// 'mapping_name' => 'groups',
					'foreign_key' => 'user_id', // 最初表与中间表的关联字段
					'relation_foreign_key' => 'role_id', // 中间表与最终表的关联字段
					'relation_table' => 'Jrrc_role_user', // 中间表名称，要求全名
					//'mapping_name'=>'enixlin',
					'mapping_fields' => 'name',
						
			)
	);

}