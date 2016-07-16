<?php
namespace Home\Model;

use Think\Model\RelationModel;

class ScheduleModel extends  RelationModel{
	protected $_link = array(        
			'user'=>array(			
							'mapping_type'=> self::BELONGS_TO,  
							'foreign_key' => 'u_id',
							'mapping_fields '=>'name',
							'as_fields'=>'name'
					),
			);
	
}