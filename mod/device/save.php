<?php
session_start();
include '../../inc/conf.php';
include '../../inc/functions.php';
if(!function_exists('checkAuth') && !function_exists('checkAccess')){
	header('Location: ../index.php');
	exit;
}
checkAuth();
checkAccess(14);
$ip = $_GET['ip'];
getModel($ip);

switch($model){
	case 'DES-3526': @snmpset($ip, wcomm, "1.3.6.1.4.1.171.12.1.2.6.0", "i", "3", timeout, 0); break;
	case 'DES-3200': @snmpset($ip, wcomm, "1.3.6.1.4.1.171.12.1.2.6.0", "i", "5", timeout, 0); break;
	case 'DES-3528': @snmpset($ip, wcomm, "1.3.6.1.4.1.171.12.1.2.6.0", "i", "5", timeout, 0); break;
}
//Запись логов в БД
logSave($ip, '', 'save');
?>
<!DOCTYPE html>
<html>
<head>
	<title>Save page</title>
	<link rel="shortcut icon" href="/img/favicon.ico" />
	<link rel="shortcut icon" href="/img/favicon.ico" /> 
	
	<!-- Bootstrap Core CSS -->
    <link href="../../bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="../../bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../../dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="../../bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
</head>
	<script type="text/javascript">
	function timer(){
	var ip = document.getElementById('ip').value;
	var obj=document.getElementById('timer_inp');
	obj.innerHTML--;
	if(obj.innerHTML==0){
		window.location = "http://95.69.244.151/index.php?ip="+ip;
		setTimeout(function(){},1000);
	}
		else{setTimeout(timer,1000);}
	}
	setTimeout(timer,1000);
	</script>
<body>

<div class="row">
<div class="col-lg-12">
	<input id="ip" type="text" value="<?=$ip?>" hidden>
	<h2>Save configuration</h2><h4>Configuration and log saved to flash.</h4>
	<p>You'll be automatically redirected to control device after <code id="timer_inp">45</code> seconds.</p>
	<a href="../">Simple Management</a>
</div>
</div>

</body>
</html>

