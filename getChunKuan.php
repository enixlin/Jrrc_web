<?php
header('Content-Type: text/html; charset=utf-8');

$cookie_file = dirname(__FILE__).'/cookie.txt';
//$cookie_file = tempnam("tmp","cookie");

//�Ȼ�ȡcookies������
$url = "http://109.0.223.182/jmpas/login.do?method=login&userName=J021016&password=394539&tiancom_zdtj=null ";
$ch = curl_init($url); //��ʼ��
curl_setopt($ch, CURLOPT_HEADER, 0); //������header����
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //�����ַ���������ֱ�����
curl_setopt($ch, CURLOPT_COOKIEJAR,  $cookie_file); //�洢cookies
curl_exec($ch);
curl_close($ch);

//ʹ�����汣���cookies�ٴη���
$url = "http://109.0.223.182/jmpas/login.do?method=selectjs&jsdh=7&style=1";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file); //ʹ�������ȡ��cookies
$response = curl_exec($ch);
curl_close($ch);

echo $response;
?>