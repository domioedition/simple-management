<?
//если кто то пытается получить доступ к файлу, редиректим его в корень сайта.
if(empty($ip)){
	header('Location: ../index.php');
	exit;
}


if(isset($port)){
	//Проверяем вхождение порта в массив 1-24.
	checkPort($port);
}

//Поиск в БД адреса
$sql = "SELECT `address` FROM nodes WHERE ip_address='$ip'";
if($result = $link->query($sql)){
	$row = $result->fetch_assoc();
	$address = $row['address'];
}
else
{
	$address = "Неизвестный адрес. Отсутствует В базе.";
}

//Запрос sysName
$sysName = snmpwalkoid($ip, rcomm, "1.3.6.1.2.1.1.5");
$sysName = str_replace("STRING: ","",implode(",", $sysName));
//Запрос sysLocation (карта)
$sysLocation = snmpwalkoid($ip, rcomm, "1.3.6.1.2.1.1.6");
$sysLocation = str_replace("STRING: ","",implode(",", $sysLocation));
//Запрос sysContact  (кольцо)
$sysContact = snmpwalkoid($ip, rcomm, "1.3.6.1.2.1.1.4");
$sysContact = str_replace("STRING: ","",implode(",", $sysContact));
//переменная location обьединяет в себе sysLocation и sysContact
$sysLocation = $sysLocation.' - '.$sysContact;
?>
<div class="row">
    <div class="col-lg-12">
    <h3 class="page-header"><?=$address;?></h3>
	<p>
		<input type="text" id="ip" value="<?=$ip?>" hidden>
		<input type="text" id="port" value="<?=$port?>" hidden>
	</p>
    </div>
</div>

<div class="row">
<div class="col-lg-12">
    <div class="panel panel-info">
    <div class="panel-heading">Device information</div>
    <div class="panel-body">
	
		<div class="row">
		<div class="col-lg-6">
		<p><?=$sysName?></p>
		<p><?=$sysLocation?></p>
		<p><?=$model?></p>
		</div>
		<div class="col-lg-6">
		<?
		if($_SESSION['userAccess']>=14)
		{
		?>
			<a href="../index.php?ip=<?=$ip?>"><button type="button" class="btn btn-outline btn-default btn-sm">Show ports</button></a>
			<a href="../index.php?page=logs"><button type="button" class="btn btn-outline btn-default btn-sm">Logs</button></a>
			<a href="../index.php?ip=<?=$ip?>&action=topology"><button type="button" class="btn btn-outline btn-default btn-sm">Topology</button></a>
			<a href="../index.php?ip=<?=$ip?>&action=clearErrors"><button type="button" class="btn btn-outline btn-default btn-sm">Clear Errors</button></a>
			<a href="../mod/device/save.php?ip=<?=$ip;?>"><button type="button" class="btn btn-outline btn-default btn-sm">Save</button></a>
<!-- 			<button class="btn btn-outline btn-default btn-sm" onclick="searchInReports()">Search in Reports</button> -->
		<?
		}
		else
		{
		?>
			<!--Menu for support-->
			<a href="../index.php?ip=<?=$ip?>"><button type="button" class="btn btn-outline btn-default btn-sm">Show ports</button></a>
			<a href="../index.php?ip=<?=$ip?>&action=clearErrors"><button type="button" class="btn btn-outline btn-default btn-sm">Clear Errors</button></a>				
		<?
		}
		?>
		</div>
		</div>

    </div>			
    </div>
</div>
</div>




<?
//если указан ip адрес свича и неуказан порт
//выводим список портов
if($action == ''){
//Запрос статус порта, выставленная скорость, скорость на котороый порт поднялся
	switch($model)
	{
		case 'DES-3526':
			//Админ. статус порта
			$portAdminState = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.11.64.1.2.4.2.1.3");
			//Выставленная скорость на порту
			$portCtrlNwayState = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.11.64.1.2.4.2.1.4");
			//Скорость линка на которой порт поднялся
			$portNwayStatus = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.11.64.1.2.4.1.1.5");
			break;
		case 'DES-3200':
			$portAdminState = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.11.113.1.5.2.2.2.1.3");
			$portCtrlNwayState = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.11.113.1.5.2.2.2.1.4");
			$portNwayStatus = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.11.113.1.5.2.2.1.1.5");
			break;
		case 'DES-3528':
			$portAdminState = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.11.105.1.2.3.2.1.4");
			$portCtrlNwayState = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.11.105.1.2.3.2.1.5");
			$portNwayStatus = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.11.105.1.2.3.1.1.6");
			break;
	}
	//Обрезаем кол-во портов до 24 штук	
	array_splice($portAdminState, 24);
	array_splice($portCtrlNwayState, 24);
	array_splice($portNwayStatus, 24);
?>
<div class="row">
<div class="col-lg-6">
<div class="panel panel-default">
	<div class="panel-heading">Table ports</div>
	<div class="panel-body">
		<div class="table-responsive">
			<table class="table table-hover">
				<thead>
					<tr>
					<th>Port</th>
					<th>State</th>
					<th>Settings/Speed/Duplex</th>
					<th>Connection</th>
					</tr>
				</thead>
				<tbody>
				<?php
					for ($i = 1; $i < 25; $i++){
					$portState = substr($portAdminState[$i-1],8,9);
					$pSpeed = substr($portNwayStatus[$i-1],8,9);
					$pSpeedState = substr($portCtrlNwayState[$i-1],8,9);
					#Админ. статус порта
					switch($portState){
						case 2: $portState = 'Disable';	break;
						case 3: $portState = 'Enable'; break;
					}
					#Выставленная скорость на порту
					if($model == 'DES-3526'){
						switch($pSpeedState){
							case 2: $pSpeedState = 'Auto'; break;
							case 3: $pSpeedState = '10Mbps-Half'; break;
							case 4: $pSpeedState = '10Mbps-Full'; break;
							case 5: $pSpeedState = '100Mbps-Half'; break;
							case 6: $pSpeedState = '100Mbps-Full'; break;
							case 7: $pSpeedState = '1Gigabps-Half'; break;
							case 8: $pSpeedState = '1Gigabps-Full'; break;
						}
					}
					if($model == 'DES-3200'){
						switch($pSpeedState){
							case 1: $pSpeedState = 'Auto'; break;
							case 2: $pSpeedState = '10Mbps-Half'; break;
							case 3: $pSpeedState = '10Mbps-Full'; break;
							case 4: $pSpeedState = '100Mbps-Half'; break;
							case 5: $pSpeedState = '100Mbps-Full'; break;
							case 6: $pSpeedState = '1Gigabps-Half'; break;
							case 7: $pSpeedState = '1Gigabps-Full'; break;
						}
					}
					if($model == 'DES-3528'){
						switch($pSpeedState){
							case 2: $pSpeedState = 'Auto'; break;
							case 3: $pSpeedState = '10Mbps-Half'; break;
							case 4: $pSpeedState = '10Mbps-Full'; break;
							case 5: $pSpeedState = '100Mbps-Half'; break;
							case 6: $pSpeedState = '100Mbps-Full'; break;
							case 7: $pSpeedState = '1Gigabps-Half'; break;
							case 8: $pSpeedState = '1Gigabps-Full'; break;
						}
					}
					#Скорость линка на которой порт поднялся
					if($model == 'DES-3526'){
						switch($pSpeed){
							case 2: $pSpeed = '<button type="button" class="btn btn-default btn-xs">Link Down</button>'; break;
							case 3: $pSpeed = '<button type="button" class="btn btn-warning btn-xs">10Mbps-Half</button>';	break;
							case 4: $pSpeed = '<button type="button" class="btn btn-warning btn-xs">10Mbps-Full</button>'; break;
							case 5: $pSpeed = '<button type="button" class="btn btn-warning btn-xs">100Mbps-Half</button>'; break;	
							case 6: $pSpeed = '<button type="button" class="btn btn-success btn-xs">100Mbps-Full</button>'; break;
						}
					}
					if($model == 'DES-3200'){
						switch($pSpeed){
							case 1: $pSpeed = '<button type="button" class="btn btn-default btn-xs">Link Down</button>'; break;
							case 2: $pSpeed = '<button type="button" class="btn btn-warning btn-xs">10Mbps-Half</button>'; break;
							case 3: $pSpeed = '<button type="button" class="btn btn-warning btn-xs">10Mbps-Full</button>'; break;
							case 4: $pSpeed = '<button type="button" class="btn btn-warning btn-xs">100Mbps-Half</button>'; break;
							case 5: $pSpeed = '<button type="button" class="btn btn-success btn-xs">100Mbps-Full</button>'; break;
						}
					}
					if($model == 'DES-3528'){
						switch($pSpeed){
							case 0: $pSpeed = '<button type="button" class="btn btn-default btn-xs">Link Down</button>'; break;
							case 4: $pSpeed = '<button type="button" class="btn btn-warning btn-xs">10Mbps-Half</button>';	break;
							case 2: $pSpeed = '<button type="button" class="btn btn-warning btn-xs">10Mbps-Full</button>'; break;
							case 8: $pSpeed = '<button type="button" class="btn btn-warning btn-xs">100Mbps-Half</button>'; break;
							case 6: $pSpeed = '<button type="button" class="btn btn-success btn-xs">100Mbps-Full</button>'; break;
						}
					}
					echo <<<LINE
					<tr onclick="window.location.href='?ip=$ip&action=viewPort&port=$i'">
					<td><button type="button" class="btn btn-default btn-xs disabled">$i</button></td>
					<td>$portState</td>
					<td>$pSpeedState</td>
					<td>$pSpeed</td>
					</tr>
LINE;
					}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>
</div>
<?php
if($_SESSION['userAccess']>=14){
?>
<div class="col-lg-6">
<div class="panel panel-default">
<div class="panel-heading">Port Info</div>
<div class="panel-body">
<div class="table-responsive">
<table class="table table-hover">
<thead>
<tr>
<th>Port</th>
<th><br></th>
</tr>
</thead>
<tbody>
<?php
for ($i = 1; $i < 25; $i++)
{
	$sql = "SELECT * FROM logs_ports_comments where ip_address='$ip' AND port ='$i'";
	$result = $link->query($sql);
	$row = $result->fetch_assoc();

	if($row != NULL){
		$ip_address = $row['ip_address'];
		$port = $row['port'];
		$comment = '<code>'.$row['comment'].'</code>';
	} else {
		$comment = "";
	}
/*echo '<tr>
		<td><button type="button" class="btn btn-default btn-xs">Reconnect</button></td>
		<td><button type="button" class="btn btn-default btn-xs">Auto</button></td>
		<td><button type="button" class="btn btn-default btn-xs">100</button></td>
		<td><button type="button" class="btn btn-default btn-xs">10</button></td>
	</tr>';*/
echo <<<LINE
<tr onclick="window.location.href='?ip=$ip&action=viewComment&port=$i'">
<td width="50px"><button type="button" class="btn btn-default btn-xs disabled">$i</button></td>
<td>$comment</td>
</tr>
LINE;
}
$link->close();
?>
</tbody>
</table>
</div>
</div>
</div>
</div>
<?php
}
?>

</div>
<!-- end of row -->

<?	
}


if($action == 'viewPort' && !empty($port)){
	
	//Если не указан порт заначиваем программу и выдаем сообщение об этом.
	if(!isset($port))
	{
		die('<hr><h1>Error: Unknown port</h1>');
	}
	/*
		Запрос на свич, который присваивает в 3 переменные статус порта, скорость выставленную на порту, скорость на которой линк поднялся.	
	*/
	// switch($model)
	// 	{
	// 	case 'DES-3526':
	// 		//Админ. статус порта
	// 		$portAdminState = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.11.64.1.2.4.2.1.3.$port");
	// 		//Выставленная скорость на порту
	// 		$portCtrlNwayState = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.11.64.1.2.4.2.1.4.$port");
	// 		//Скорость линка на которой порт поднялся
	// 		$portNwayStatus = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.11.64.1.2.4.1.1.5.$port");					
	// 		break;
	// 	case 'DES-3200':
	// 		$portAdminState = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.11.113.1.5.2.2.2.1.3.$port");
	// 		$portCtrlNwayState = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.11.113.1.5.2.2.2.1.4.$port");
	// 		$portNwayStatus = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.11.113.1.5.2.2.1.1.5.$port");
	// 		break;
	// 	case 'DES-3528':
	// 		$portAdminState = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.11.105.1.2.3.2.1.4.$port.1");
	// 		$portCtrlNwayState = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.11.105.1.2.3.2.1.5.$port.1");
	// 		$portNwayStatus = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.11.105.1.2.3.1.1.6.$port.1");
	// 		break;
	// 	}
	// /*
	// 	Выбираем из массива последнюю цифру, и приводим переменную к типу string
	// */	
	// 	$portAdminState = substr($portAdminState[0],8,9);
	// 	$portNwayStatus = substr($portNwayStatus[0],8,9);
	// 	$portCtrlNwayState = substr($portCtrlNwayState[0],8,9);
	// /*
	// 	Определяем статус порта
	// */
	// 	switch($portAdminState){
	// 		case 2: $portAdminState = 'Disable'; break;
	// 		case 3: $portAdminState = 'Enable'; break;
	// 	}
	// /*
	// 	Определяем выставленную скорость на порту
	// */
	// 	if($model == 'DES-3526'){
	// 		switch($portCtrlNwayState){
	// 			case 2: $portCtrlNwayState = 'Auto'; break;
	// 			case 3: $portCtrlNwayState = '10Mbps-Half'; break;
	// 			case 4: $portCtrlNwayState = '10Mbps-Full'; break;
	// 			case 5: $portCtrlNwayState = '100Mbps-Half'; break;
	// 			case 6: $portCtrlNwayState = '100Mbps-Full'; break;
	// 			case 7: $portCtrlNwayState = '1Gigabps-Half'; break;
	// 			case 8: $portCtrlNwayState = '1Gigabps-Full'; break;
	// 		}
	// 	}
	// 	if($model == 'DES-3200'){
	// 		switch($portCtrlNwayState){
	// 			case 1: $portCtrlNwayState = 'Auto'; break;
	// 			case 2: $portCtrlNwayState = '10Mbps-Half'; break;
	// 			case 3: $portCtrlNwayState = '10Mbps-Full'; break;
	// 			case 4: $portCtrlNwayState = '100Mbps-Half'; break;
	// 			case 5: $portCtrlNwayState = '100Mbps-Full'; break;
	// 			case 6: $portCtrlNwayState = '1Gigabps-Half'; break;
	// 			case 7: $portCtrlNwayState = '1Gigabps-Full'; break;
	// 		}
	// 	}
	// 	if($model == 'DES-3528'){
	// 		switch($portCtrlNwayState){
	// 			case 2: $portCtrlNwayState = 'Auto'; break;
	// 			case 3: $portCtrlNwayState = '10Mbps-Half'; break;
	// 			case 4: $portCtrlNwayState = '10Mbps-Full'; break;
	// 			case 5: $portCtrlNwayState = '100Mbps-Half'; break;
	// 			case 6: $portCtrlNwayState = '100Mbps-Full'; break;
	// 			case 7: $portCtrlNwayState = '1Gigabps-Half'; break;
	// 			case 8: $portCtrlNwayState = '1Gigabps-Full'; break;
	// 		}
	// 	}
	// /*
	// 	Определяем скорость на которой линк поднялся
	// */
		
	// 	if($model == 'DES-3526'){
	// 		switch($portNwayStatus){
	// 			case 2: $portNwayStatus = '<button type="button" class="btn btn-default btn-sm">Link Down</button>'; break;
	// 			case 3: $portNwayStatus = '<button type="button" class="btn btn-warning btn-sm">10Mbps-Half</button>';	break;
	// 			case 4: $portNwayStatus = '<button type="button" class="btn btn-warning btn-sm">10Mbps-Full</button>'; break;
	// 			case 5: $portNwayStatus = '<button type="button" class="btn btn-warning btn-sm">100Mbps-Half</button>'; break;	
	// 			case 6: $portNwayStatus = '<button type="button" class="btn btn-success btn-sm">100Mbps-Full</button>'; break;
	// 		}
	// 	}
	// 	if($model == 'DES-3200'){
	// 		switch($portNwayStatus){
	// 			case 1: $portNwayStatus = '<button type="button" class="btn btn-default">Link Down</button>'; break;
	// 			case 2: $portNwayStatus = '<button type="button" class="btn btn-warning">10Mbps-Half</button>'; break;
	// 			case 3: $portNwayStatus = '<button type="button" class="btn btn-warning">10Mbps-Full</button>'; break;
	// 			case 4: $portNwayStatus = '<button type="button" class="btn btn-warning">100Mbps-Half</button>'; break;
	// 			case 5: $portNwayStatus = '<button type="button" class="btn btn-success">100Mbps-Full</button>'; break;
	// 		}
	// 	}
	// 	if($model == 'DES-3528'){
	// 		switch($portNwayStatus){
	// 			case 0: $portNwayStatus = '<button type="button" class="btn btn-default">Link Down</button>'; break;
	// 			case 4: $portNwayStatus = '<button type="button" class="btn btn-warning">10Mbps-Half</button>';	break;
	// 			case 2: $portNwayStatus = '<button type="button" class="btn btn-warning">10Mbps-Full</button>'; break;
	// 			case 8: $portNwayStatus = '<button type="button" class="btn btn-warning">100Mbps-Half</button>'; break;
	// 			case 6: $portNwayStatus = '<button type="button" class="btn btn-success">100Mbps-Full</button>'; break;
	// 		}
	// 	}
		
	/*Запрос port description*/	
	@$portDescription = snmpwalk($ip, rcomm, "1.3.6.1.2.1.31.1.1.1.18.".$port);
	$portDescription = str_replace("STRING: ","", $portDescription[0]);
	$pos = strripos($portDescription, "IP:");
	if($pos === false){
		$client_ip = "No ip address in description.";
	}else{
		$client_ip = explode(":", $portDescription);
		$client_ip = str_replace("CL", "", $client_ip['1']);
	}
	$pos = strripos($portDescription, "CL:");
	if($pos === false){
		$client_id = "";
		$client_id = "Unknown ClientID.";
	}else{
		$client_id = explode(":", $portDescription);
		$client_id = "<a target=_blank href=https://billing.airbites.net.ua/clients/?View=SEE&Name=$client_id[2]>$client_id[2]</a>";
	}
	//$portDescription = explode(":", $portDescription);
	//@$clientIp = substr($portDescription[1],0,strlen($portDescription[1])-2);
	//@$clientId = "<a target=_blank href=https://billing.airbites.net.ua/clients/?View=SEE&Name=$portDescription[2]>$portDescription[2]</a>";
	$clientIp = $client_ip;
	$clientId = $client_id;
	
	//Запрос bandwidth_control
		switch($model){
			case 'DES-3526':
				$bandRx = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.11.64.1.2.6.1.1.2");
				$bandTx = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.11.64.1.2.6.1.1.3");
				$bandRx = substr($bandRx[$port-1],8,9);
				$bandTx = substr($bandTx[$port-1],8,9);
				if($bandRx == 0) $bandRx='no_limit';
				if($bandTx == 0) $bandTx='no_limit';
				break;

			case 'DES-3200':
				$bandRx = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.11.113.1.5.2.3.1.1.2");
				$bandTx = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.11.113.1.5.2.3.1.1.3");
				$bandRx = substr($bandRx[$port-1],8,9)*0.0009765625;
				$bandTx = substr($bandTx[$port-1],8,9)*0.0009765625;
				if($bandRx == 1024000) $bandRx='no_limit';
				if($bandTx == 1024000) $bandTx='no_limit';
				break;

			case 'DES-3528':
				$bandRx = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.12.61.3.1.1.2");
				$bandTx = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.12.61.3.1.1.3");
				$bandRx = substr($bandRx[$port-1],8,9)*0.0009765625;
				$bandTx = substr($bandTx[$port-1],8,9)*0.0009765625;
				if($bandRx == 0) $bandRx='no_limit';
				if($bandTx == 0) $bandTx='no_limit';
				break;
		}
?>



<div class="row">
<div class="col-lg-12">
<div class="panel panel-info">
<div class="panel-heading">Port information</div>
<div class="panel-body">
	<div class="row">
		<div class="col-lg-3">
		<h4>Switch</h4>
		<p> ip: <?=$ip;?></p>
		<p> port: <?=$port;?></p>	
		</div>
		<div class="col-lg-3">
		<h4>Client</h4>
		<p> ip: <code><?=$clientIp;?></code></p>
		<p> id: <code><?=$clientId;?></code></p>	
		</div>
		<div class="col-lg-3">
		<h4>Bandwidth control</h4>
		<p>RX Rate: <code><?=$bandRx;?></code></p>
		<p>TX Rate: <code><?=$bandTx;?></code></p>
		</div>
	</div>
</div>
</div>
</div>
</div>

<div class="row">
<div class="col-lg-6">
<?
	echo get_access_ip($ip, $port, $model);
	if($_SESSION['userAccess']>=14){
		// echo 'test';
	}
?>
</div>
</div>

	<div class="row">
	<div class="col-lg-12">
	<div class="panel panel-info">
	<div class="panel-heading">Port status</div>
	<div class="panel-body">
	<div class="row" id="portStatus">
		<div class="col-lg-6">
		<?
			include ($_SERVER['DOCUMENT_ROOT'].'/mod/device/showPort.php');
		?>
			<script type="text/javascript">
				// var timerId = setTimeout(function check() {
				// 	checkPortStatus();
				// 	console.log('Checking port status...');
				// 	timerId = setTimeout(check, 2000);
				// }, 2000);
				// clearTimeout(6000000); //таймаут 10 минут.
			</script>
		</div>
		<div class="col-lg-6">
		<?
		if($_SESSION['userAccess']>=14)
		{
		?>
			<p>
				<a href="mod/device/modPortCtrl.php?ip=<?=$ip?>&port=<?=$port?>&action=reconnect"><button type="button" class="btn btn-outline btn-default btn-sm">Reconnect</button></a>
			</p>
			<p>
				<a href="mod/device/modPortCtrl.php?ip=<?=$ip?>&port=<?=$port?>&state=enable"><button type="button" class="btn btn-outline btn-default btn-sm">Enable</button></a>
				<a href="mod/device/modPortCtrl.php?ip=<?=$ip?>&port=<?=$port?>&state=disable"><button type="button" class="btn btn-outline btn-default btn-sm">Disable</button></a>
			</p>
			<p>
				<a href="mod/device/modPortCtrl.php?ip=<?=$ip?>&port=<?=$port?>&speed=auto"><button type="button" class="btn btn-outline btn-default btn-sm">Auto</button></a>
				<a href="mod/device/modPortCtrl.php?ip=<?=$ip?>&port=<?=$port?>&speed=100_full"><button type="button" class="btn btn-outline btn-default btn-sm">100_full</button></a>
				<a href="mod/device/modPortCtrl.php?ip=<?=$ip?>&port=<?=$port?>&speed=10_full"><button type="button" class="btn btn-outline btn-default btn-sm">10_full</button></a>
			</p>
			<p>
				<a href="../index.php?ip=<?=$ip?>&action=viewPort&port=<?=$port?>&mod=iptv"><button type="button" class="btn btn-outline btn-default btn-sm">IPTV</button></a>
				<a href="../index.php?ip=<?=$ip?>&action=viewPort&port=<?=$port?>&mod=bandwidth"><button type="button" class="btn btn-outline btn-default btn-sm">Bandwidth</button></a>
			</p>
		<?
		}
		else
		{
		?>
			<!--Menu for support-->
			<p>
				<a href="mod/device/modPortCtrl.php?ip=<?=$ip?>&port=<?=$port?>&action=reconnect"><button type="button" class="btn btn-outline btn-default btn-sm">Reconnect</button></a>
			</p>
			<p>
				<a href="mod/device/modPortCtrl.php?ip=<?=$ip?>&port=<?=$port?>&speed=auto"><button type="button" class="btn btn-outline btn-default btn-sm">Auto</button></a>
				<a href="mod/device/modPortCtrl.php?ip=<?=$ip?>&port=<?=$port?>&speed=100_full"><button type="button" class="btn btn-outline btn-default btn-sm">100_full</button></a>
				<a href="mod/device/modPortCtrl.php?ip=<?=$ip?>&port=<?=$port?>&speed=10_full"><button type="button" class="btn btn-outline btn-default btn-sm">10_full</button></a>
			</p>
			<p>
				<a href="../index.php?ip=<?=$ip?>&action=viewPort&port=<?=$port?>&mod=iptv"><button type="button" class="btn btn-outline btn-default btn-sm">IPTV</button></a>
			</p>
		<?
		}
		?>
		</div>
	</div>
	<hr>
	<div class="row">
	<div class="col-lg-12">
		<button type="button" class="btn btn-outline btn-primary btn-sm" onclick="showErrors()">ShowErrors</button>
		<button type="button" class="btn btn-outline btn-primary btn-sm" onclick="cableDiag()">CableDiag</button>
		<button type="button" class="btn btn-outline btn-primary btn-sm" onclick="showMac()">ShowMac</button>
		<button type="button" class="btn btn-outline btn-primary btn-sm" onclick="showUtilization()">showUtilization</button>
		<!--<button type="button" class="btn btn-outline btn-primary btn-sm" onclick="check10Access()">check10Access</button>-->
		<a href="../index.php?ip=<?=$ip?>&action=viewPort&port=<?=$port?>&mod=port_security"><button type="button" class="btn btn-outline btn-danger btn-sm">Reconnect port_security</button></a>
	</div>
	</div>
	<div class="row" id="result">
		<div class="col-lg-12">
		</div>
	</div>

	<div class="row">
	<div class="col-lg-12">
	<hr>
<?php
//Подключения модулей для работы с портом
if(isset($mod)){
	switch($mod){
/*		case 'mac':
			include('showMac.php');
			break;
		case 'errors':
			include('showErrors.php');			
			break;
		case 'cablediag':
			include('showCableDiag.php');
			break;*/
		case 'iptv':
			include('showIPTV.php');
			break;
		case 'port_security':
			port_security($ip, $port, $model);
			break;
			
case 'bandwidth' :
echo <<<line
<h4>Change Bandwidth</h4>
<form action="mod/device/modPortCtrl.php?ip=$ip&port=$port" method="post">
<input name="rx" placeholder="rx" value="100">
<input name="tx" placeholder="tx" value="100">
<input name="action" value="bandwidth" hidden>
<input type="submit" value="Set Bandwidth">
</form>
line;
break;
	}
}



?>
	
	
	</div>
	</div>
				
				
				
		
				
				
				
	</div>
	</div>
	
	</div>
</div>


		<?
	
}

//Создаем коментарий для порта
if($action == 'viewComment' && !empty($port)){
	
	$sql = "SELECT * FROM logs_ports_comments where ip_address='$ip' AND port ='$port'";
	$result = $link->query($sql);
	$row = $result->fetch_assoc();

	if($row != NULL){
		$ip_address = $row['ip_address'];
		$port = $row['port'];
		$comment = $row['comment'];
	}
	
echo <<<form
<form action="../mod/port_comments.php" method="post">
	<div class="form-group">
	<input name="ip_address" value="$ip" hidden>
	<input name="port" value="$port" hidden>
	<label>Comments - ip-address: <code>$ip</code> - port: <code>$port</code></label>
	<textarea class="form-control" rows="3" name="comment">$comment</textarea>
</div>
<div class="form-group">
	<button type="submit" class="btn btn-default">Оставить комментарий</button>
</div>
</form>
form;
}



//Очистка ошибок и статистики со свича.
if($action == 'clearErrors'){
	clearErrors($ip, $port);
	//Запись логов в БД
	logSave($ip, $port, "Errors cleared.");	
}

//Топология свича.
if($action == 'topology'){
	include('modTopology.php');
}

if($action == 'lldp'){
	include('modLldp.php');
}
?>