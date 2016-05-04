<?php
namespace Home\Model;

use Think\Model\RelationModel;
class YwlsfixedModel extends RelationModel{
	protected  $tableName='ywls_fixed';
	protected $_link = array (
				
			//关联规则
			'struction' => array (
					'mapping_type' => self::BELONGS_TO,
					'foreign_key' => 'upbranch',
					'mapping_fields'=>'br_name',
					'as_fields'=>'br_name'

			),
	);
}