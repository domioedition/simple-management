<?
session_start();
include('../../inc/conf.php');
include('../../inc/functions.php');


if($_SERVER['REQUEST_METHOD'] == "POST"){
	$ip = $_POST['ip'];
	$port = $_POST['port'];
	getModel();
	echo get_access_ip($ip, $port, $model);
}else{
	Error(1);
}

?>