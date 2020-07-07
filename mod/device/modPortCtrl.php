<?php
session_start();
error_reporting(0);
include('../../inc/conf.php');
include('../../inc/functions.php');
checkAuth();






$ip = $_GET['ip'];
$port = $_GET['port'];
@$state = $_GET['state'];
@$speed = $_GET['speed'];

//Запрашиваем функцию getModel(), чтобы узнать модель свича
getModel();


if($_SERVER['REQUEST_METHOD'] == 'GET'){
	$action = $_GET['action'];
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	$action = $_POST['action'];
}

//Модуль установки ширины канала(bandwidth)
if($action == 'bandwidth'){
	checkAccess();//проверка доустпа на использование.
	checkPort($port);//проверяем или порт входит в массив.
	$rx = $_POST['rx'];
	$tx = $_POST['tx'];
	if(isset($rx)&&isset($tx)){
		switch($model){
			case 'DES-3526':
				$request = snmpset($ip,wcomm,"1.3.6.1.4.1.171.11.64.1.2.6.1.1.2.$port", "i", $rx);
				$request = snmpset($ip,wcomm,"1.3.6.1.4.1.171.11.64.1.2.6.1.1.3.$port", "i", $tx);
				$log = 'Rx-Tx: '.$rx .' - ' . $tx;
				break;
			case 'DES-3200':
				$rx = $rx/0.0009765625;
				$tx = $tx/0.0009765625;
				$request = snmpset($ip,wcomm,"1.3.6.1.4.1.171.11.113.1.5.2.3.1.1.2.$port", "i", $rx);
				$request = snmpset($ip,wcomm,"1.3.6.1.4.1.171.11.113.1.5.2.3.1.1.3.$port", "i", $tx);
				$log = 'Rx-Tx: '.$rx .' - ' . $tx;
				break;
			case 'DES-3528':
				$rx = $rx/0.0009765625;
				$tx = $tx/0.0009765625;
				$request = snmpset($ip,wcomm,"1.3.6.1.4.1.171.12.61.3.1.1.2.$port", "i", $rx);
				$request = snmpset($ip,wcomm,"1.3.6.1.4.1.171.12.61.3.1.1.3.$port", "i", $tx);
				$log = 'Rx-Tx: '.$rx .' - ' . $tx;
				break;	
		}
	}
	//Запись логов в БД
	logSave($ip, $port, $log);
}



//Отправляем запрос на Вкл./Выкл порта
if(isset($state)){
	checkAccess();//проверка доустпа на использование.
	checkPort($port);//проверяем или порт входит в массив.

	switch($state){
		case 'enable' :
			if($model == 'DES-3526') snmpset($ip,wcomm,"1.3.6.1.2.1.2.2.1.7.".$port,'i',1);
			if($model == 'DES-3200') snmpset($ip,wcomm,"1.3.6.1.4.1.171.11.113.1.5.2.2.2.1.3.".$port.".100",'i',3);
			if($model == 'DES-3528') snmpset($ip,wcomm,"1.3.6.1.4.1.171.11.105.1.2.3.2.1.4.".$port.".1",'i',3);
			break;
		case 'disable' :
			if($model == 'DES-3526') snmpset($ip,wcomm,"1.3.6.1.2.1.2.2.1.7.".$port,'i',2);
			if($model == 'DES-3200') snmpset($ip,wcomm,"1.3.6.1.4.1.171.11.113.1.5.2.2.2.1.3.".$port.".100",'i',2);
			if($model == 'DES-3528') snmpset($ip,wcomm,"1.3.6.1.4.1.171.11.105.1.2.3.2.1.4.".$port.".1",'i',2);
		break;
	}
	//Запись логов в БД
	logSave($ip, $port, $state);
}

//Отправляем запрос на смену скокрости порта Авто/100фулл/10Фулл
if(isset($speed)){
	checkPort($port);//проверяем или порт входит в массив.
	switch($speed){
		case 'auto':
			if($model == 'DES-3526') snmpset($ip,wcomm,"1.3.6.1.4.1.171.11.64.1.2.4.2.1.4.".$port, 'i', 2);
			if($model == 'DES-3200') snmpset($ip,wcomm,"1.3.6.1.4.1.171.11.113.1.5.2.2.2.1.4.".$port.".100",'i',1);
			if($model == 'DES-3528') snmpset($ip,wcomm,"1.3.6.1.4.1.171.11.105.1.2.3.2.1.5.".$port.".1",'i',2);
			break;
		case '100_full':
			if($model == 'DES-3526') snmpset($ip,wcomm,"1.3.6.1.4.1.171.11.64.1.2.4.2.1.4.".$port, 'i', 6);
			if($model == 'DES-3200') snmpset($ip,wcomm,"1.3.6.1.4.1.171.11.113.1.5.2.2.2.1.4.".$port.".100",'i',5);
			if($model == 'DES-3528') snmpset($ip,wcomm,"1.3.6.1.4.1.171.11.105.1.2.3.2.1.5.".$port.".1",'i',6);		
			break;
		case '10_full':
			if($model == 'DES-3526') snmpset($ip,wcomm,"1.3.6.1.4.1.171.11.64.1.2.4.2.1.4.".$port, 'i', 4);
			if($model == 'DES-3200') snmpset($ip,wcomm,"1.3.6.1.4.1.171.11.113.1.5.2.2.2.1.4.".$port.".100",'i',3);
			if($model == 'DES-3528') snmpset($ip,wcomm,"1.3.6.1.4.1.171.11.105.1.2.3.2.1.5.".$port.".1",'i',4);		
			break;
	}
	//Запись логов в БД
	logSave($ip, $port, $speed);
}

//Функиця reconnect, переподключение порта.
if($action == 'reconnect'){
	checkPort($port);//проверяем или порт входит в массив.
	//Чистим арп таблицу.
	$clear_arp = snmpset($ip,wcomm,"1.3.6.1.4.1.171.12.1.2.12.1.0","i",2);
	//Админ. статус порта
	switch($model){
		case 'DES-3526': $portAdminState = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.11.64.1.2.4.2.1.3.$port"); break;
		case 'DES-3200': $portAdminState = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.11.113.1.5.2.2.2.1.3.$port"); break;
		case 'DES-3528': $portAdminState = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.11.105.1.2.3.2.1.4.$port.1"); break;
	}
	$portAdminState = substr($portAdminState[0],8,9);
	//если статус порта включен, то отсылаем mib на переподключение
	if($portAdminState != '2') {
		//Выключаем порт!!!
		switch($model){
			case 'DES-3526':snmpset($ip,wcomm,"1.3.6.1.2.1.2.2.1.7.".$port,'i',2); break;
			case 'DES-3200':snmpset($ip,wcomm,"1.3.6.1.4.1.171.11.113.1.5.2.2.2.1.3.".$port.".100",'i',2); break;
			case 'DES-3528':snmpset($ip,wcomm,"1.3.6.1.4.1.171.11.105.1.2.3.2.1.4.".$port.".1",'i',2); break;
		}
		//Устанавливаем задержку 3 секунды;
		//sleep(3);
		//Включем порт!!!		
		switch($model){
			case 'DES-3526':snmpset($ip,wcomm,"1.3.6.1.2.1.2.2.1.7.".$port,'i',1); break;
			case 'DES-3200':snmpset($ip,wcomm,"1.3.6.1.4.1.171.11.113.1.5.2.2.2.1.3.".$port.".100",'i',3); break;
			case 'DES-3528':snmpset($ip,wcomm,"1.3.6.1.4.1.171.11.105.1.2.3.2.1.4.".$port.".1",'i',3); break;
		}
		//Чистим арп таблицу.
		$clear_arp = snmpset($ip,wcomm,"1.3.6.1.4.1.171.12.1.2.12.1.0","i",2);
		//Запись логов в БД
		logSave($ip, $port, $action);
	}
}

sleep(2);
//перенаправление на страницу с указанием порта и ip свича
header("Location: ../../index.php?ip=$ip&action=viewPort&port=$port");
?>
