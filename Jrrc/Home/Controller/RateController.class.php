<?php
namespace  Home\Controller;


use Think\Controller;
class RateController extends Controller{
	public function index(){
		$this->display('index');
	}
	
	public function task_setup(){
		$model=M("task_type");
		$result=$model->select();
		$this->assign("result",$result);
		$this->display("task_setup");
	}
		
	/**
	 * 取得所有的经营机构
	 */
	public function get_all_unit() {
		$model = M ( 'struction' );
		$condition ['class'] = 1;
		$condition ['status'] = '0';
		$condition ['br_id'] = array (
				array (
						'neq',
						'08129'
				),
				array (
						'neq',
						'08131'
				)
		);
		$map ['br_id'] = '08108';
		$map ['_logic'] = 'or';
		$map ['_complex'] = $condition;
		$result = $model->where ( $map )->select ();
		//dump($result);
		return $result;
	}
		
	
	/**
	 * 取得经营单位的业务任务
	 */
	public  function get_task($unit,$zb_type){
		$model=D("task");
		$condition['unit_id']=$unit;
		$condition['zb_type']=$zb_type;
		$result=$model->relation(true)->where($condition)->select();
		//dump($result);
		return $result;
	}
		
	
	/**
	 * 显示任务数据
	 * @param unknown $zb_type
	 */
	public function task_list($zb_type){
		$units=$this->get_all_unit();
		$arraylist=array();
		foreach ($units as $u){
		$arraylist[$u['br_id']]=$this->get_task($u['br_id'], $zb_type);
		$arraylist[$u['br_id']]['unit_name']=$u['br_name'];
		$arraylist[$u['br_id']]['unit_id']=$u['br_id'];
		}
		dump($arraylist);
		foreach($arraylist as &$a){			
			if(!isset($a[0])) {	
				$a[0]['zb_year']=0;
				$a[0]['q1']=0;
				$a[0]['q2']=0;
				$a[0]['q3']=0;
				$a[0]['q4']=0;
				$a[0]['zb_type']=$zb_type;
			}
		}
		unset($a);
		//dump($arraylist);
		return $arraylist;
	}
	
	public function show_task_list($zb_type){
		$result=$this->task_list($zb_type);
		//dump($result);
		$this->assign("list",$result);
		$this->display("task_list");
	}
	
	//处理增加任务指标
	public function add_task($zb_name,$zb_type){
		$model=M('task_type');
		$condition['zb_name']=$zb_name;
		$condition['zb_type']=$zb_type;
		$result=$model->add($condition);
		 if($result!=false){
			$this->ajaxReturn ( "1" );
		 }
		
	}
	
	public  function delete_task_type($zb_type){
		$model=M('task_type');
		$condition['zb_type']=$zb_type;
		$result=$model->delete($condition);
	}
	
	//显示处理增加任务指标页面
	public function show_add_task(){		
		$this->display('add_task');
	}
	
	
	public function show_task_type_list(){
		$model=M('task_type');
		$result=$model->select();
		$this->assign("result",$result);
		$this->display('task_type_list');
	}	
	
}