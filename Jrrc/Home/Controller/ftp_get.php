
<?php
$ftp_server='221.231.138.40';
$ftp_user_name='ibdp2011';
$ftp_user_pass='enixlin1981';
//$result=ftp_connect('221.231.138.40');

// path to remote file
 $remote_file  =  'london.xml' ;
 $local_file  =  'localfile.xml' ;
 
// open some file to write to
 $handle  =  fopen ( $local_file ,  'w' );
 
// set up basic connection
 $conn_id  =  ftp_connect ( $ftp_server );
 
// login with username and password
 $login_result  =  ftp_login ( $conn_id ,  $ftp_user_name ,  $ftp_user_pass );
 
// try to download $remote_file and save it to $handle
 if ( ftp_fget ( $conn_id ,  $handle ,  $remote_file ,  FTP_ASCII ,  0 )) {
 echo  "successfully written to  $local_file \n" ;
} else {
 echo  "There was a problem while downloading  $remote_file  to  $local_file \n" ;
}
ftp_close ( $conn_id );
fclose ( $handle );


$reader = new XMLReader();
$reader->open($local_file);
$countElements = 0;

while ($reader->read()){
	if($reader->nodeType == XMLReader::ELEMENT){
		$nodeName = $reader->name;
	}
	if($reader->nodeType == XMLReader::TEXT && !empty($nodeName)){
		switch($nodeName){
			case 'name':
				echo $name = $reader->value."</br>";
				break;
			case 'lat':
				echo $channel = $reader->value."</br>";
				break;
			case 'countryName':
				echo $start = $reader->value."</br>";
				break;
			
		}
	}
}
$reader->close();



// close the connection and the file handler

 ?> 