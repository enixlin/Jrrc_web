<?php

namespace Home\Controller;

use Think\Controller;

class Update1Controller extends Controller {
	
	public function index(){
		$FileName="../Jrrc_web/Public/data/ifx_iss_cust.dat";
		$this->ReadFile($FileName);
		
	}
		
	/*
	 * ftp获取数据流水文件
	 *
	 * $ftp 服务器地址
	 * $name 用户名
	 * $password 密码
	 */
	public function getftpfile() {
		//融和ftp登录信息
		$ftp_server='141.0.189.69';
		$ftp_user_name='gj';
		$ftp_user_password='gj';
	
		// 连接
		$conn_id = ftp_connect ( $ftp_server );
		// 登录
		$login_result = ftp_login ( $conn_id, $ftp_user_name, $ftp_user_password );
	
		// 获取目录下所有的文件夹
		$dir = ftp_nlist ( $conn_id, "/" );
	
		// path to remote file
		// 远程和本地文件夹定义
		$remote_directory=$dir[count($dir)-1]."/";
		// 业务流水
		$remote_file_ywls = $remote_directory . 'ifx_iss_cfmbusi.dat';
		$local_file_ywls = '../Jrrc_web/Public/data/ifx_iss_cfmbusi.dat';
		// 机构信息
		$remote_file_jg = $remote_directory . 'c_brchno.dat';
		$local_file_jg = '../Jrrc_web/Public/data/c_brchno.dat';
		// 客户属地
		$remote_file_kusd = $remote_directory . 'ifx_iss_cust.dat';
		$local_file_kusd = '../Jrrc_web/Public/data/ifx_iss_cust.dat';
		// 存款信息
		$remote_file_cxrj = $remote_directory . 'fc_balance_avg.dat';
		$local_file_cxrj  = '../Jrrc_web/Public/data/fc_balance_avg.dat';
	
		// 文件读写句柄
		$handle_ywls = fopen ( $local_file_ywls, 'w' );
		$handle_jg = fopen ( $local_file_jg, 'w' );
		$handle_kusd = fopen ( $local_file_kusd, 'w' );
		$handle_cxrj = fopen ( $local_file_cxrj, 'w' );
	
		// 文件下载成功标志
		$flag_ywls = null;
		$flag_jg = null;
		$flag_kusd = null;
		$flag_cxrj = null;
	
		// try to download $remote_file and save it to $handle
		if (ftp_fget ( $conn_id, $handle_ywls, $remote_file_ywls, FTP_ASCII, 0 )) {
			echo "【业务流水】成功下载   </br>";
			$flag_ywls = 1;
		} else {
			echo "【业务流水】下载失败   </br>";
			$flag_ywls = 0;
		}
		if (ftp_fget ( $conn_id, $handle_jg, $remote_file_jg, FTP_ASCII, 0 )) {
			echo "【机构信息表】成功下载   </br>";
			$flag_jg = 1;
		} else {
			echo "【机构信息表】下载失败   </br>";
			$flag_jg = 0;
		}
		if (ftp_fget ( $conn_id, $handle_kusd, $remote_file_kusd, FTP_ASCII, 0 )) {
			echo "【客户属地表】成功下载   </br>";
			$flag_kusd = 1;
		} else {
			echo "【客户属地表】下载失败   </br>";
			$flag_kusd = 0;
		}
		if (ftp_fget ( $conn_id, $handle_cxrj, $remote_file_cxrj, FTP_ASCII, 0 )) {
			echo "【存款信息表】成功下载   </br>";
			$flag_cxrj = 1;
		} else {
			echo "【存款信息表】下载失败   </br>";
			$flag_cxrj = 0;
		}
		
		// 关闭ftp连接
		ftp_close ( $conn_id );
		fclose ( $handle_ywls );
		fclose ( $handle_jg );
		fclose ( $handle_kusd );
		fclose ( $handle_cxrj );
		
		//返回执行结果
		if($flag_jg==1 && $flag_kusd==1 && $flag_ywls==1 && $flag_cxrj==1){
			return 1;
		}else{
			return 0;
		}
	}
	
	/*
	 * 读取已下载到本地的数据文件，生成一个二维数组$data返回
	 * 
	 * 
	 * */
	public function ReadFile($FileName){
		//二维数组对象
		$data=array();
		// 文件读写句柄
		$handle = fopen ( $FileName, 'r' );
		if ( $handle ) {
			while (( $buffer  =  fgets ( $handle ,  14096 )) !==  false ) {
				$buffer = str_replace ( '藎', '|', $buffer );
				$buffer = str_replace ( '脇', '|', $buffer );
				$record = explode ( "|", $buffer );
				array_push($data, $record);
			}
			if (! feof ( $handle )) {
				echo  "Error: unexpected fgets() fail\n" ;
			}
			fclose ( $handle );
		}
		dump($data);
		
	}
	
	
	
	
}