<?php

namespace Home\Controller;

require ("../Jrrc_web/Public/Classes/PHPExcel.php");
use Think\Controller;

class UpdateController extends Controller {
	public function index() {
		$this->display ( 'index' );
	}
	public function update() {
		// 比较日期，如果需要就更新数据库
		if (! $this->comparedate ()) {
			// 无需更新数据
			echo "</br>";
			echo "数据已为最新，无需更新！";
		} else {
			// 下载文件
			if ($this->getftpfile ()) {
				echo "文件下载成功</br>";
				// 打开数据库连接
				if (! $connect = $this->connectdatabase ()) {
					echo "打开数据库失败</br>";
				} else {
					// 导入数据
					$ywls = '../Jrrc_web/Public/data/ifx_iss_cfmbusi.dat';
					$table_ywls = 'jrrc_ywls';
					$struction = '../Jrrc_web/Public/data/c_brchno.dat';
					$table_struction = 'jrrc_struction';
					$location = '../Jrrc_web/Public/data/ifx_iss_cust.dat';
					$table_location = 'jrrc_location';
					$balance = '../Jrrc_web/Public/data/fc_balance_avg.dat';
					$table_balance = 'jrrc_cx_avg_current';
					$tf="../Jrrc_web/Public/data/iss_filetf10.dat";
					$table_tf="jrrc_tf";
					
					$updatecount = 0;
					$import_flag = 0;
					
					if ($updatecount = $this->importdata ( $ywls, $table_ywls, $connect )) {
						echo "更新业务流水成功：共" . $updatecount . "条记录</br>";
						$updatecount = 0;
						$import_flag ++;
					}
					
					if ($updatecount = $this->importdata ( $struction, $table_struction, $connect )) {
						echo "更新机构表成功：共" . $updatecount . "条记录</br>";
						$updatecount = 0;
						$import_flag ++;
					} else {
						echo "更新失败</br>";
					}
					
					if ($updatecount = $this->importdata ( $location, $table_location, $connect )) {
						echo "更新客户属地表成功：共" . $updatecount . "条记录</br>";
						$updatecount = 0;
						$import_flag ++;
					} else {
						echo "更新失败</br>";
					}
					
					if ($updatecount = $this->importdata ( $balance, $table_balance, $connect )) {
						echo "更新存款余额表成功：共" . $updatecount . "条记录</br>";
						$updatecount = 0;
						$import_flag ++;
					} else {
						echo "更新失败</br>";
					}
					
					if ($updatecount = $this->importdata ( $tf, $table_tf, $connect )) {
						echo "更新贸易融资表成功：共" . $updatecount . "条记录</br>";
						$updatecount = 0;
						$import_flag ++;
					} else {
						echo "更新失败</br>";
					}
					
					$date=str_replace ( "/", '', $this->getftpfiledate () );
					if($updatecount =$this->updateChunKuan($date-2)){
						echo "更新外币各项存款日均数据成功，共新增数据：".$updatecount."条记录 </br>";
						$updatecount = 0;
					}else{
						echo "外币存款日均数据更新失败</br>";
					}
					
					echo $import_flag;
					
					// 更新数据库
					if ($import_flag != 5) {
						$this->redirect ( '/Home/Update/updateerror' );
					} else {
						// 所有临时表写入后，开如补充流水中的经营单位信息
						$this->add_ywls_fixed ();
						
						$this->makelog ( str_replace ( "/", '', $this->getftpfiledate () ) );
						// $this->redirect('/Home/Chart/index');
					}
				}
			}
		}
	}
	
	// 打开数据库连接参数
	public function connectdatabase() {
		$host = 'localhost';
		$username = 'root';
		$password = 'root';
		// 打开数据库连接
		$connect = mysql_connect ( $host, $username, $password );
		return $connect;
	}
	
	/*
	 * 写入更新日志
	 *
	 */
	public function makelog($data_date) {
		$log = M ( 'updatelog' );
		$data ['data_date'] = $data_date;
		$data ['update_date'] = time ();
		$result = $log->add ( $data );
	}
	
	/**
	 * 更新存款数据，从天维系统抓取数据
	 */
	public function updateChunKuan($date) {
		//如果更新的日期早于数据库已有的记录日期则返回，不用更新
// 		$lastdate=str_replace ( "/", '', $this->getftpfiledate () );
// 		if($date<=$lastdate){
// 			echo "date update";
// 			return 0;
// 		}
		$cookie_file = dirname ( __FILE__ ) . '/cookie.txt';
		
		// 先获取cookies并保存
		$url = "http://109.0.223.182/jmpas/login.do?method=login&userName=J021016&password=394539&tiancom_zdtj=null ";
		$ch = curl_init ( $url ); // 初始化
		curl_setopt ( $ch, CURLOPT_HEADER, 0 ); // 不返回header部分
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true ); // 返回字符串，而非直接输出
		curl_setopt ( $ch, CURLOPT_COOKIEJAR, $cookie_file ); // 存储cookies
		curl_exec ( $ch );
		curl_close ( $ch );
				
																						
// 																						model_name:wbckyjtjb
// 																						currentPage:
// 																						queryConTipsValue:
// 																						tjrq:20160424
// 																						sdbs:4
// 																						bz:0C
// 																						tjkj:1
// 																						page:1
// 																						rows:50
		
		
		// 使用上面保存的cookies再次访问
		$url = "http://109.0.223.182/jmpas/studio/queryParser.do?method=queryData&funId=wbckyjtjb&isForPage=true&model_name=wbckyjtjb&queryConTipsValue=null&tjrq=".$date."&sdbs=4&bz=0C&tjkj=1&page=1&rows=50";
		$ch = curl_init ( $url );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $ch, CURLOPT_COOKIEFILE, $cookie_file ); // 使用上面获取的cookies
		$response = curl_exec ( $ch );
		curl_close ( $ch );
		
		$json = json_decode ( $response );
		
		//如果返回的记录数为0，则代表没有当天的数据
		if ($json->total = 0) {
			return 0;
		} else {
			$model = M ( "waibichunkuan_avg" );
			$addrowcount=0;
			foreach ($json->rows as $jr){				
				$condition['total_base']=$jr->{'各项存款余额基数'};
				$condition['save_base']=$jr-> {'储蓄存款余额基数'};
				$condition['company_base']=$jr->{'对公存款余额基数'};
				$condition['total_balance']=$jr->{'各项存款余额'};
				$condition['save_balance']=$jr->{'储蓄存款余额'};
				$condition['company_balance']=$jr->{'对公存款余额'};
				$condition['total_create']=$jr->{'各项存款新增'};
				$condition['save_create']=$jr->{'储蓄存款新增'};
				$condition['company_create']=$jr->{'对公存款新增'};
				$condition['JGMC']=$jr->JGMC;
				$condition['JGDH']=str_replace("N", "", $jr->JGDH);
				$condition['TJRQ']=$jr->TJRQ;
				
				$result=$model->add($condition);
				$addrowcount++;				
			}
			return $addrowcount;
		}
	}
	public function updateerror() {
		echo "写入更新日志失败";
	}
	
	/*
	 * ftp获取数据流水文件
	 *
	 * $ftp 服务器地址
	 * $name 用户名
	 * $password 密码
	 */
	public function getftpfile() {
		
		// 融和ftp登录信息
		$ftp_server = '141.0.189.69';
		$ftp_user_name = 'gj';
		$ftp_user_password = 'gj';
		
		// 连接
		$conn_id = ftp_connect ( $ftp_server );
		// 登录
		$login_result = ftp_login ( $conn_id, $ftp_user_name, $ftp_user_password );
		
		// 获取目录下所有的文件夹
		$dir = ftp_nlist ( $conn_id, "/" );
		
		// path to remote file
		// 远程和本地文件夹定义
		$remote_directory = $dir [count ( $dir ) - 1] . "/";
		$backup_dir_date = $this->getftpfiledate ();
		$backup_dir = "../Jrrc_web/Public/data/backup/" . $backup_dir_date;
		// 创建目录
		mkdir ( $backup_dir );
		// $backup_dir=$backup_dir.substr_replace($backup_ dir, "/", 0);
		// dump($backup_dir);
		
		// 业务流水
		$remote_file_ywls = $remote_directory . 'ifx_iss_cfmbusi.dat';
		$local_file_ywls = '../Jrrc_web/Public/data/ifx_iss_cfmbusi.dat';
		$local_file_ywls_backup = '../Jrrc_web/Public/data/backup' . $backup_dir_date . '/ifx_iss_cfmbusi.dat';
		// 机构信息
		$remote_file_jg = $remote_directory . 'c_brchno.dat';
		$local_file_jg = '../Jrrc_web/Public/data/c_brchno.dat';
		$local_file_jg_backup = '../Jrrc_web/Public/data/backup' . $backup_dir_date . '/c_brchno.dat';
		// 客户属地
		$remote_file_kusd = $remote_directory . 'ifx_iss_cust.dat';
		$local_file_kusd = '../Jrrc_web/Public/data/ifx_iss_cust.dat';
		$local_file_kusd_backup = '../Jrrc_web/Public/data/backup' . $backup_dir_date . '/ifx_iss_cust.dat';
		// 存款信息
		$remote_file_cxrj = $remote_directory . 'fc_balance_avg.dat';
		$local_file_cxrj = '../Jrrc_web/Public/data/fc_balance_avg.dat';
		$local_file_cxrj_bakcup = '../Jrrc_web/Public/data/backup' . $backup_dir_date . '/fc_balance_avg.dat';
		
		//贸易融资
		$remote_file_tf=$remote_directory.'iss_filetf10.dat';
		$local_file_tf="../Jrrc_web/Public/data/iss_filetf10.dat";
		$local_file_tf_bakcup = '../Jrrc_web/Public/data/backup' . $backup_dir_date . '/iss_filetf10.dat';
		
		// 文件读写句柄
		$handle_ywls = fopen ( $local_file_ywls, 'w' );
		$handle_jg = fopen ( $local_file_jg, 'w' );
		$handle_kusd = fopen ( $local_file_kusd, 'w' );
		$handle_cxrj = fopen ( $local_file_cxrj, 'w' );
		$handle_tf = fopen ( $local_file_tf, 'w' );
		// 备份文件读写句柄
		$handle_ywls_backup = fopen ( $local_file_ywls_backup, 'w' );
		$handle_jg_backup = fopen ( $local_file_jg_backup, 'w' );
		$handle_kusd_backup = fopen ( $local_file_kusd_backup, 'w' );
		$handle_cxrj_backup = fopen ( $local_file_cxrj_bakcup, 'w' );
		$handle_tf_backup = fopen ( $local_file_tf_bakcup, 'w' );
		
		// 文件下载成功标志
		$flag_ywls = null;
		$flag_jg = null;
		$flag_kusd = null;
		$flag_cxrj = null;
		$flag_tf = null;
		
		
		// try to download $remote_file and save it to $handle
		if (ftp_fget ( $conn_id, $handle_ywls, $remote_file_ywls, FTP_ASCII, 0 )) {
			ftp_fget ( $conn_id, $handle_ywls_backup, $remote_file_ywls, FTP_ASCII, 0 );
			echo "【业务流水】成功下载   </br>";
			$flag_ywls = 1;
		} else {
			echo "【业务流水】下载失败   </br>";
			$flag_ywls = 0;
		}
		if (ftp_fget ( $conn_id, $handle_jg, $remote_file_jg, FTP_ASCII, 0 )) {
			ftp_fget ( $conn_id, $handle_jg_backup, $remote_file_jg, FTP_ASCII, 0 );
			echo "【机构信息表】成功下载   </br>";
			$flag_jg = 1;
		} else {
			echo "【机构信息表】下载失败   </br>";
			$flag_jg = 0;
		}
		if (ftp_fget ( $conn_id, $handle_kusd, $remote_file_kusd, FTP_ASCII, 0 )) {
			ftp_fget ( $conn_id, $handle_kusd_backup, $remote_file_kusd, FTP_ASCII, 0 );
			echo "【客户属地表】成功下载   </br>";
			$flag_kusd = 1;
		} else {
			echo "【客户属地表】下载失败   </br>";
			$flag_kusd = 0;
		}
		if (ftp_fget ( $conn_id, $handle_cxrj, $remote_file_cxrj, FTP_ASCII, 0 )) {
			ftp_fget ( $conn_id, $handle_cxrj_backup, $remote_file_cxrj, FTP_ASCII, 0 );
			echo "【存款信息表】成功下载   </br>";
			$flag_cxrj = 1;
		} else {
			echo "【存款信息表】下载失败   </br>";
			$flag_cxrj = 0;
		}
		if (ftp_fget ( $conn_id, $handle_tf, $remote_file_tf, FTP_ASCII, 0 )) {
			ftp_fget ( $conn_id, $handle_tf_backup, $remote_file_tf, FTP_ASCII, 0 );
			echo "【贸易融资流水表】成功下载   </br>";
			$flag_tf = 1;
		} else {
			echo "【贸易融资流水表】下载失败   </br>";
			$flag_tf = 0;
		}
		
		// 关闭ftp连接
		ftp_close ( $conn_id );
		fclose ( $handle_ywls );
		fclose ( $handle_jg );
		fclose ( $handle_kusd );
		fclose ( $handle_cxrj );
		fclose ( $handle_tf );
		
		fclose ( $handle_ywls_backup );
		fclose ( $handle_jg_backup );
		fclose ( $handle_kusd_backup );
		fclose ( $handle_cxrj_backup );
		fclose ( $handle_tf_backup );
		
		// 返回执行结果
		if ($flag_jg == 1 && $flag_kusd == 1 && $flag_ywls == 1 && $flag_cxrj == 1&& $flag_tf==1) {
			return 1;
		} else {
			return 0;
		}
	}
	
	/*
	 * 导入数据文件到数据库
	 * 参数：
	 * $fiel文件名（包括路径）
	 * $tablename数据表的名
	 * $connect可用的数据库连接
	 */
	public function importdata($file, $tablename, $connect) {
		// 打开文件
		$handle = fopen ( $file, 'r' );
		
		// 选择要用的数据库
		mysql_query ( 'use jrrc', $connect );
		// 设定字符集
		mysql_query ( 'set names utf8', $connect );
		
		// 清空数据库表
		
		mysql_query ( 'truncate ' . $tablename, $connect );
		
		$string = "insert into " . $tablename . " values";
		$val = '';
		$recordcount = 0;
		$improtcount = 0;
		if ($handle) {
			while ( ($buffer = fgets ( $handle, 24096 )) !== false ) {
				
				if ($recordcount == 1000) {
					$string = substr ( $string, 0, - 1 );
					mysql_query ( $string, $connect );
					// echo $string;
					$string = "insert into " . $tablename . " values";
					$recordcount = 0;
				}
				// 转码
				// $buffer = mb_convert_encoding ( $buffer, "utf-8", "gbk, gb2312" );
				
				$buffer = str_replace ( '藎', '|', $buffer );
				$buffer = str_replace ( '脇', '|', $buffer );
				$record = explode ( "|", $buffer );
				// echo count($record);
				$val = $val . "(";
				foreach ( $record as $value ) {
					$val = $val . "'" . $value . "',";
				}
				$val = substr ( $val, 0, - 1 );
				$val = $val . ")";
				$string = $string . $val . ",";
				$val = "";
				
				$recordcount ++;
				// echo $recordcount."</br>";
			}
			$string = substr ( $string, 0, - 1 );
			mysql_query ( $string, $connect );
			 //echo $string;
			if (! feof ( $handle )) {
				echo "Error: unexpected fgets() fail\n";
			}
			fclose ( $handle );
			$sql = "select * from " . $tablename;
			
			return mysql_num_rows ( mysql_query ( $sql, $connect ) );
		}
	}
	
	/*
	 * 读取更新记录
	 * 返回数据日期
	 *
	 */
	public function getUpdateLog() {
		$log = M ( 'updatelog' );
		$result = $log->order ( 'id desc' )->limit ( 1 )->select ();
		return $result [0] ['data_date'];
	}
	
	/*
	 *
	 *
	 * ftp获取数据流水文件的日期
	 *
	 *
	 */
	function getftpfiledate() {
		// 融和ftp登录信息
		$ftp_server = '141.0.189.69';
		$ftp_user_name = 'gj';
		$ftp_user_password = 'gj';
		
		// 连接
		$conn_id = ftp_connect ( $ftp_server );
		// 登录
		$login_result = ftp_login ( $conn_id, $ftp_user_name, $ftp_user_password );
		
		// 获取目录下所有的文件夹
		$dir = ftp_nlist ( $conn_id, "/" );
		
		// path to remote file
		// 远程和本地文件夹定义
		$remote_directory = $dir [count ( $dir ) - 1] . "/";
		return $remote_directory;
	}
	
	/*
	 *
	 * 比较ftp数据文件日期与数据库更新记录
	 *
	 *
	 */
	public function comparedate() {
		$logdate = $this->getUpdateLog ();
		$ftpfiledate = str_replace ( "/", '', $this->getftpfiledate () );
		
		echo "数据库记录日期 :" . $logdate . "</br>";
		echo "数据文件日期:" . $ftpfiledate . "</br>";
		
		if ($ftpfiledate > $logdate) {
			return $needupdate = true;
		} else {
			return $needupdate = false;
		}
	}
	
	/*
	 * 将客户属地表补充一级、二级支行的行号
	 *
	 */
	public function fixedLocation() {
		$model_location = M ( 'location' );
		$model_struction = M ( 'struction' );
		
		$result_location = $model_location->select ();
		$result_struction = $model_struction->select ();
		
		foreach ( $result_location as &$rl ) {
			foreach ( $result_struction as $rs ) {
				if ($rl ['br_id'] == $rs ['br_id']) {
					// 上级行的行号不是总行部门
					if ($rs ['p_id'] != '08101') {
						$rl ['upbranch'] = $rs ['p_id'];
						$rl ['br_id'] = $rs ['br_id'];
					} else {
						$rl ['upbranch'] = $rs ['br_id'];
						$rl ['br_id'] = $rs ['br_id'];
					}
					if ($rs ['br_id'] == '08108') {
						$rl ['upbranch'] = $rs ['br_id'];
						$rl ['br_id'] = $rs ['br_id'];
					}
				}
			}
		}
		unset ( $rl );
		// dump( $result_location);
		return $result_location;
	}
	
	/*
	 * 生成完整的业务流水，如果有业务分成的，按分成的比例生成一条单独的流水
	 *
	 */
	public function fixedywls() {
		$model_ywls = M ( 'ywls' );
		
		// 将大于指定日期$date的数据读取出来
		$lockdate = $this->findLockDate ();
		// echo $lockdate."条锁定的数据记录<br/>";
		
		$map ['yw_date'] = array (
				array (
						'GT',
						$lockdate ['lockdate_end'] 
				),
				array (
						'LT',
						$lockdate ['lockdate_start'] 
				),
				'or' 
		)
		;
		
		$result_ywls = $model_ywls->where ( $map )->select ();
		
		$result_location = $this->fixedLocation ();
		
		$result_ywls_fixed = array ();
		
		// 将个人业务补充完毕，补充一级和二级行号，业务比例都设为100
		foreach ( $result_ywls as $key => &$ry ) {
			if ($ry ['er_type'] != '1') {
				if ($ry ['bl_brno'] == '08108') {
					$ry ['upbranch'] = $ry ['bl_brno'];
					$ry ['branch'] = $ry ['bl_brno'];
					$ry ['precent'] = '100';
					array_push ( $result_ywls_fixed, $ry );
					unset ( $result_ywls [$key] );
				} else {
					$ry ['upbranch'] = $ry ['bl_upbr'];
					$ry ['branch'] = $ry ['bl_brno'];
					$ry ['precent'] = '100';
					array_push ( $result_ywls_fixed, $ry );
					unset ( $result_ywls [$key] );
				}
			}
		}
		// echo COUNT($result_ywls)."业务流水数</br>";
		
		// 历遍所有对公业务
		// 对公的结算和结售汇业务都按分成比例生成
		
		foreach ( $result_location as $rl ) {
			
			foreach ( $result_ywls as $key => &$ry ) {
				
				// 客户号相符
				if ($ry ['custno'] == $rl ['c_id']) {
					$ry ['upbranch'] = $rl ['upbranch'];
					$ry ['branch'] = $rl ['br_id'];
					$ry ['precent'] = $rl ['precent'];
					// $ry_s=array($ry);
					array_push ( $result_ywls_fixed, $ry );
					// $ry ['pct']用于累加业务分成比例，
					// 当比例累加达到100时，该业务记录不用再分成,可以删除
					// $ry['pct']=$ry['pct']+$ry['precent'];
					// if($ry['pct']==100){
					// unset($result_ywls[$key]);
					// }
				}
			}
		}
		// echo count($result_ywls_fixed);
		return $result_ywls_fixed;
	}
	
	/*
	 * 将整理之后的流水记录写入数据库表jrrc_ywls_fixed
	 */
	public function add_ywls_fixed() {
		$result_ywls_fixed = $this->fixedywls ();
		
		// 删除jrrc_ywls_fixed表中没有加锁的数据
		$this->deleteUnLock ();
		
		// dump($result_ywls_fixed[count($result_ywls_fixed)-1]);
		$sql = "insert into jrrc_ywls_fixed  values ";
		$sql_string = '';
		$count = 0;
		
		foreach ( $result_ywls_fixed as $ryf ) {
			$sql_string = $sql_string . '( ';
			// $sql_string='( ';
			foreach ( $ryf as $v ) {
				$sql_string = $sql_string . "'" . $v . "',";
			}
			$sql_string = substr ( $sql_string, 0, - 1 );
			
			$sql_string = $sql_string . ", '','0'),";
			$count ++;
			if ($count == 100) {
				$sql_string = substr ( $sql_string, 0, - 1 );
				$sql = $sql . $sql_string;
				$model = M ();
				$model->query ( $sql );
				$count = 0;
				$sql = "insert into jrrc_ywls_fixed  values ";
				$sql_string = '';
			}
		}
		$sql_string = substr ( $sql_string, 0, - 1 );
		$sql = $sql . $sql_string;
		
		$model = M ();
		$model->query ( $sql );
	}
	
	// 对指定日期以前的业务进行加锁，加锁后不能删除和修改
	public function lockRecord($lockDate_start, $lockDate_end) {
		$model = M ( 'ywls_fixed' );
		$model_lock = M ( 'record_lock' );
		
		$model_lock->where ( 1 )->delete ();
		$d ['lockdate_start'] = $lockDate_start;
		$d ['lockdate_end'] = $lockDate_end;
		$model_lock->add ( $d );
		
		$date ['lock'] = 1;
		$map ['yw_date'] = array (
				array (
						'ELT',
						$lockDate_end 
				),
				array (
						'EGT',
						$lockDate_start 
				) 
		);
		return $result = $model->where ( $map )->save ( $date );
	}
	
	// 对指定日期以前的业务进行解锁，解锁后才能删除和修改
	public function unLockRecord($lockDate_start, $lockDate_end) {
		$model = M ( 'ywls_fixed' );
		$model_lock = M ( 'record_lock' );
		$model_lock->where ( 1 )->delete ();
		
		$date ['lock'] = 0;
		$map ['yw_date'] = array (
				array (
						'ELT',
						$lockDate_end 
				),
				array (
						'EGT',
						$lockDate_start 
				) 
		);
		return $result = $model->where ( $map )->save ( $date );
	}
	
	// 删除没有加锁的数据
	public function deleteUnLock() {
		$model = M ( 'ywls_fixed' );
		$map ['lock'] = 0;
		return $result = $model->where ( $map )->delete ();
	}
	
	// 查找记录数据锁定的日期
	public function findLockDate() {
		$model_lock = M ( 'record_lock' );
		return $model_lock->find ();
	}
	
	// 显示加锁和解锁的页面
	public function showLockSetup() {
		$this->display ( 'setLock' );
	}
	
	/**
	 * 更新海关进出口数据
	 * 步骤： 1.上传相关格式的EXCEL文件
	 * 2.读取excel文件
	 * 3.更新服务器数据
	 */
	public function UpdateCustomer() {
		$this->UpLoadExcel ();
	}
	// 上传相关格式的EXCEL文件
	public function Handle_UpLoadExcel() {
		$upload = new \Think\Upload ();
		// 实例化上传类
		$upload->maxSize = 3145728;
		// 设置附件上传大小
		$upload->exts = array (
				'jpg',
				'gif',
				'png',
				'jpeg',
				'xls',
				'xlsx' 
		);
		// 设置附件上传类型
		// $upload->savePath = '../Jrrc_web/Public/Upload/';
		// 设置附件上传目录 // 上传文件
		$info = $upload->upload ();
		if (! $info) {
			// 上传错误提示错误信息
			$this->error ( $upload->getError () );
		} else {
			// 上传成功
			$this->success ( '上传成功！' );
		}
	}
	public function show_UpLoadExcel() {
		$this->display ( "upload" );
	}
	
	
	
}