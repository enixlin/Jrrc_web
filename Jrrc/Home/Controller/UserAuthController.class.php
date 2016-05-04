<?php
namespace Home\Controller;
use Common\Controller\AuthController;
use Think\Model;


class UserAuthController extends AuthController {
	public function index() {
		$this->display('index');
	}
	
	//显示用户维护页面
	public function showUserAuth(){
		$model=M('auth_group');
		$result=$model->select();
		
		$user=M('user');
		$result_user=$user->select();
		
		$this->assign('roler',$result);
		$this->assign('user',$result_user);
		$this->display('showUserIndex');
	}
		
	
	//设定用户的状态
	public function setUserStatus($id,$status){
		$model=M('user');
		$conditon['id']=$id;
		$conditon['status']=$status;
		return $model->save($conditon);
		 
	}
	
	public function handlesetUserStatus($id,$status){
		$result=$this->setUserStatus($id, $status);
			$this->showUserList();
		
	}
	
	
	//显示用户列表页面
	public function showUserList(){
		$user=M('user');
		$result_user=$user->select();
		$this->assign('user',$result_user);
		$this->display('showUserlist');
	}
	
	//添加用户
	public function addUser($name,$password,$type){
		$result=$this->handleAddUser($name, $password, $type);
		//dump($result);
		if($result!=0){
			$this->ajaxReturn($result);
		}else{
			$this->ajaxReturn(0);
		}
	}
	
	//处理添加用户
	public function handleAddUser($name,$password,$type){
		//先检查一下是否存在同名用户
		$model=D('user');
		$condition['name']=$name;
		if(0==($model->find($condition))){
			return "unfind";
		}else{
			$data['name']=$name;
			$data['password']=$password;
			$data['status']=1;
			$data['auth_group_access']=array(
					'group_id'=>$type,
			);
			return $result=$model->relation(true)->add($data);
		}
		
	}
	
	//删除用户
	public function deleteUser($id){
		$model=D('user');
		$condition['id']=$id;
		$this->ajaxReturn($model->relation(true)->where($condition)->delete());
	}
	
	
}

	
	
	
