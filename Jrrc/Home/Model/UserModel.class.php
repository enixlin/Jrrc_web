<?php

namespace Home\Model;

use Think\Model\RelationModel;

class UserModel extends RelationModel {
	protected $_link = array (
			
			//关联规则
			'auth_group' => array (
					'mapping_type' => self::MANY_TO_MANY,
					'foreign_key' => 'uid',
					'relation_table' => 'jrrc_auth_group_access' ,
					'relation_foreign_key'=>'group_id',
					'mapping_fields' => 'id,title,rules',

			),
			
	
						
			
			
			//关联规则
			'auth_group_access' => array (
					'mapping_type' => self::HAS_ONE,
					'foreign_key' => 'uid'
					
			),
				

			//关联功能
			"menu"=>array(
					'mapping_type'=>self::MANY_TO_MANY,
					'foreign_key'=>'uid',
					'relation_table'=>'jrrc_user_menu',
					'relation_foreign_key'=>'mid',
					
			),
			
			
	);
	
	
}