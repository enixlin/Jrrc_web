<?php

namespace Home\Controller;

// use Common\Controller\AuthController;
use Think\Controller;
use Think\Model;

class PhoneBookController extends Controller {
	
	public function jrrc(){
		
		$this->display('jrrc_phonebook');
	}
	
	
	// 显示电话簿页面
	public function index() {
		// 获取机构组织
		$PhoneBook = M ( 'phonebook' );
		$position = $PhoneBook->distinct ( true )->field ( "position" )->select ();
		$Department = $PhoneBook->distinct ( true )->field ( "department" )->order('department asc')->select ();
		//$sub_Department = $PhoneBook->distinct ( true )->field ( "sub_department" )->select ();
		$this->assign ( "departments", $Department );
		//$this->assign ( "sub_departments", $sub_Department );
		$this->assign ( "positions", $position );
		$this->display ( 'PhoneBook_search' );
	}
	
	//获取二级支行或部门的数据
	public function getsubdepartment(){
		$PhoneBook=M("phonebook");
		$date=$_POST;
		$map['department']=$date['department'];
		$sub_Department = $PhoneBook->where($map)->distinct ( true )->field ( "sub_department" )->select ();
		//dump($map);
		//$sub_Department = $PhoneBook->select();
		//dump($sub_Department);
		echo json_encode($sub_Department);
		
	}
	public function search() {
		$Condition = $_POST;
		$this->searchNumber ( $Condition );
	}
	// 查询电话本
	public function searchNumber($Condition) {
		$PhoneBook = M ( 'phonebook' );
		$data ['department'] = array (
				'like',
				"%" . $Condition ['company'] . "%" 
		);
		$data ['sub_department'] = array (
				'like',
				"%" . $Condition ['department'] . "%" 
		);
		$data ['position'] = array (
				'like',
				"%" . $Condition ['position'] . "%" 
		);
		$data ['name'] = array (
				'like',
				"%" . $Condition ['name'] . "%" 
		);
		$data ['id'] = array (
				'like',
				"%" . $Condition ['id'] . "%" 
		);
	
		
		$Result = $PhoneBook->where ( $data )->select ();
		// 以JSON格式返回查询的结果
		echo json_encode ( $Result );
	}
	
	// 新增通信记录界面
	public function add() {
		$this->display ( 'PhoneBook_add' );
	}
	public function addc(){
		$condition=$_POST;
		$this->addHandle($condition);
	}
	
	// 新增通信记录处理
	public function addHandle($data) {
		$PhoneBook = M ( 'phonebook' );
		$PhoneBook->create($data);
		$Result = $PhoneBook->add ( $data );
		echo  $Result ;
	}
	
	// 删除电话记录
	public function deleteRecords($Condition) {
		$PhoneBook = M ( 'phonebook' );
		$Result = $PhoneBook->where ( $Condition )->delete ();
		echo json_encode ( $Result );
	}
	public function update() {
		$Condition = $_POST;
		$this->updateRecords ( $Condition );
	}
	// 更新电话记录
	public function updateRecords($data) {
		// dump($data);
		$PhoneBook = M ( 'phonebook' );
		$PhoneBook->create ( $data );
		$result = $PhoneBook->save ( $data );
		echo $result ;
	}
}