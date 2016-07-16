<?php

namespace Home\Model;

use Think\Model\RelationModel;

class TaskModel extends RelationModel {
	protected $_link = array (
			'task_type'=>array(
					'mapping_type' => self::BELONGS_TO,
					'foreign_key' => 'zb_type',
					'mapping_fields'=>'zb_name',
					'as_fields'=>"zb_name"
			),
	);
}