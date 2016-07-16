<?php
namespace Home\Model;


use Think\Model\RelationModel;
class AuthRuleModel extends RelationModel{
	protected  $_link=array(
			'auth_rule_sub'=>array(
					'mapping_type'=>self::HAS_MANY,
					'foreign_key'=>'pid'
			),
	);
}