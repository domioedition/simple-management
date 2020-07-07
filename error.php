<?php
session_start();
require_once('inc/functions.php');
checkAuth();
@$ip = $_GET['ip'];
$login =$_SESSION['userLogin'];
include('inc/head.inc.php');

if(isset($ip)){
	$msg = '<h3>The device ip-address: <a href="index.php?ip=$ip?=/>$ip</a> is not available or does not exist.</h3>';
}

//Error code
//1 - Ошибка доступа(Даный пользователь не имет прав на выполнения данного действия)
//2 - Не доступен коммутатор по  snmp
//3 - Модель отсутвует
//4 - Данного порта нет в массиве.
@$code = $_GET['code'];


switch($code){
	case '1' : $msg = "Sorry, you do not have rights to this action. "; break;
	case '2' : $msg = "The device ip-address: <code>$ip</code> is not available or does not exist. "; break;
	case '3' : $msg = "Unknown model of device. "; break;
	case '4' : $msg = "Actions with the port are not allowed. "; break;
	default : $msg = "Unknown code. ";
}




?>

<div class="page-header">
	<h1>Error</h1>
</div>

<div class="row">
<div class="col-lg-6">
<div class="alert alert-danger">
	<?=$msg;?><a class="alert-link" href="javascript:history.back()"> Сome back.</a>
</div>
</div>
</div>
<?php
include('inc/foot.inc.php');
?>