<?
session_start();
include('../../inc/conf.php');
include('../../inc/functions.php');
function cableDiag($ip, $port, $model){

if($model == 'DES-3526'){
		//Старт диагностики
		// Запускаем тест
		$startDiagnostic = snmpset($ip, wcomm, "1.3.6.1.4.1.171.12.58.1.1.1.12.".$port, "i", 1);
		// Проверяем или он завершился
		if($startDiagnostic != 2){
			die("Error. Start diagnostic status = ".$startDiagnostic);
		}
		// Проверка активности линка(Result: 1-Link Up, 0 - Link down)
		$linkStatus =	 snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.12.58.1.1.1.3.".$port);
		//Состояние пар
		$linkPair1 =	 snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.12.58.1.1.1.4.".$port);
		$linkPair2 =	 snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.12.58.1.1.1.5.".$port);
		//$linkPair3 =	 snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.12.58.1.1.1.6.".$port);
		//$linkPair4 =	 snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.12.58.1.1.1.7.".$port);
		//Длинна кабеля 1-ая и 2-ая пары
		$linkLenghtPair1 = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.12.58.1.1.1.8.".$port);
		$linkLenghtPair2 = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.12.58.1.1.1.9.".$port);
		//$linkLenghtPair3 = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.12.58.1.1.1.10".$port);
		//$linkLenghtPair4 = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.12.58.1.1.1.11".$port);
		$linkStatus = implode(",", $linkStatus);
		$linkStatus = substr($linkStatus,8,9);

		$linkPair1 = implode(",", $linkPair1);
		$linkPair1 = substr($linkPair1,8,9);

		$linkPair2 = implode(",", $linkPair2);
		$linkPair2 = substr($linkPair2,8,9);

		$linkLenghtPair1 = implode(",", $linkLenghtPair1);
		$linkLenghtPair1 = substr($linkLenghtPair1,8,9);

		$linkLenghtPair2 = implode(",", $linkLenghtPair2);
		$linkLenghtPair2 = substr($linkLenghtPair2,8,9);

		switch($linkPair1){
			case '0' : $linkPair1 = 'OK'; break;
			case '1' : $linkPair1 = 'open'; break;
			case '2' : $linkPair1 = 'short'; break;
			case '3' : $linkPair1 = 'open-short'; break;
			case '4' : $linkPair1 = 'crosstalk'; break;
			case '5' : $linkPair1 = 'unknown'; break;
			case '6' : $linkPair1 = 'count'; break;
			case '7' : $linkPair1 = 'no-cable'; break;
			case '8' : $linkPair1 = 'Other'; break;
		}

		switch($linkPair2){
			case '0' : $linkPair2 = 'OK'; break;
			case '1' : $linkPair2 = 'open'; break;
			case '2' : $linkPair2 = 'short'; break;
			case '3' : $linkPair2 = 'open-short'; break;
			case '4' : $linkPair2 = 'crosstalk'; break;
			case '5' : $linkPair2 = 'unknown'; break;
			case '6' : $linkPair2 = 'count'; break;
			case '7' : $linkPair2 = 'no-cable'; break;
			case '8' : $linkPair2 = 'Other'; break;
		}
 

		//Вывод информации о статусе линка Up Down		
		if($linkStatus == 1) $linkStatus = 'Link Up'; else $linkStatus = 'Link Down';
		//Результаты теста
			if($linkPair1 == 'OK' && $linkPair2 == 'OK'){
				$testResult = 'OK';
			}elseif($linkPair1 == 'no-cable' && $linkPair2 == 'no-cable'){
				$testResult = 'No Cable';
			}elseif($linkPair1 == 'short'){
				$testResult = "Pair1 " . $linkPair1 . " at " . $linkLenghtPair1 . " M<br>";
			}elseif($linkPair2 == 'short'){
				$testResult = "Pair2 " . $linkPair2 . " at " . $linkLenghtPair2 . " M<br>";
			}else{
				$testResult = "Pair1 " . $linkPair1 . " at " . $linkLenghtPair1 . " M<br>" . "Pair2 " . $linkPair2 . " at " . $linkLenghtPair2 . " M";
			}
		//Вывод длинны кабеля
		if ($linkLenghtPair1 == $linkLenghtPair2 && $linkLenghtPair1 != -1 ){
			$linkLenght = '<code>'.$linkLenghtPair1.'</code>';
		}else{
			$linkLenght = '-';
		}
		//Если длиина первой пары равна -1, то это значит что в порте нет кабеля
		if($linkLenghtPair1 == -1){
			$linkLenght = 'Couldn\'t show length.';
		}
$cableDiag = "<h4>Perform Cable Diagnostics ...</h4>
			<div class=\"table-responsive\">
			<table class=\"table\">
				<thead>
				<tr>
					<th>Link Status</th>
					<th>Test Result</th>
					<th>Cable Length (M)</th>
				</tr>
				</thead>
				<tbody>
					<tr>
						<td>$linkStatus</td>
						<td>$testResult</td>
						<td>$linkLenght</td>
					</tr>
				</tbody>
			</table>";
} else {
	$cableDiag = "<h1>Only DES-3526 models.</h1>";
}
	$log = "CableDiag";
	logSave($ip, $port, $log);
	return $cableDiag;
}


if($_SERVER['REQUEST_METHOD'] == "POST"){
	$ip = $_POST['ip'];
	$port = $_POST['port'];
	getModel();
	echo cableDiag($ip, $port, $model);
	// sleep(1);
}else{
	Error(1);
}
?>
