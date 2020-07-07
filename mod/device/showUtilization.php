<?php
session_start();
include('../../inc/conf.php');
include('../../inc/functions.php');

function showUtilization($ip, $port){
	$snmpPreviousRX = snmpwalk($ip, rcomm, "1.3.6.1.2.1.2.2.1.10.".$port);
	$snmpPreviousTX = snmpwalk($ip, rcomm, "1.3.6.1.2.1.2.2.1.16.".$port);
	$snmpPreviousRX = str_replace('Counter32: ', '', $snmpPreviousRX[0]);
	$snmpPreviousTX = str_replace('Counter32: ', '', $snmpPreviousTX[0]);
	sleep(1);
	$snmpCurrentRX = snmpwalk($ip, rcomm, "1.3.6.1.2.1.2.2.1.10.".$port);
	$snmpCurrentTX = snmpwalk($ip, rcomm, "1.3.6.1.2.1.2.2.1.16.".$port);
	$snmpCurrentRX = str_replace('Counter32: ', '', $snmpCurrentRX[0]);
	$snmpCurrentTX = str_replace('Counter32: ', '', $snmpCurrentTX[0]);
	$rx = abs(($snmpCurrentRX - $snmpPreviousRX)/1);
	$tx = abs(($snmpCurrentTX - $snmpPreviousTX)/1);
	$rx = $rx/1024/1024*8;
	$tx = $tx/1024/1024*8;
	$rx = number_format($rx,2,",", " ");
	$tx = number_format($tx,2,",", " ");
	$result = $rx.":".$tx;
	return $result;
}
if($_SERVER['REQUEST_METHOD'] == "POST"){
	$ip = $_POST['ip'];
	$port = $_POST['port'];
	getModel();
	echo showUtilization($ip, $port);
}else{
	// echo "Error!";
}
	$ip = $_GET['ip'];
	$port = $_GET['port'];
	getModel();
	echo showUtilization($ip, $port);
?>