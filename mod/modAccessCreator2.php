<?
checkAccess(14);
if($_SERVER['REQUEST_METHOD'] == 'POST'){
	$swip = trim($_POST['swip']);
	$port = trim($_POST['port']);
	$userip = trim($_POST['userip']);
}else{
	$swip = '';
	$port = '';
	$userip = '';
}
?>

<h1>Access Creator</h1>
<div class="row">
<div class="col-lg-4">
<p>Для создания 10-го правила достаточно ввести ip-адрес свича, номер порта и ip-адрес пользователя.</p>
<form action="" method="post" role="form">
<div class="form-group">	
	<input type="text" name="swip" placeholder="switch ip" class="form-control" value="<?=$swip?>">
</div>
<div class="form-group">
	<input type="text" name="port" placeholder="port" class="form-control" value="<?=$port?>">
</div>
<div class="form-group">
	<input type="text" name="userip" placeholder="user ip" class="form-control" value="<?=$userip?>">
</div>
<div class="form-group">
	<button type="submit" class="btn btn-primary">Create</button>
</div>
</form>
</div>
</div>
<?php
/*
	Access creator2
*/


$portsArr = array(
	'80000000' => '1',
	'40000000' => '2',
	'20000000' => '3',
	'10000000' => '4',
	'08000000' => '5',
	'04000000' => '6',
	'02000000' => '7',
	'01000000' => '8',
	'00800000' => '9',
	'00400000' => '10',
	'00200000' => '11',
	'00100000' => '12',
	'00080000' => '13',
	'00040000' => '14',
	'00020000' => '15',
	'00010000' => '16',
	'00008000' => '17',
	'00004000' => '18',
	'00002000' => '19',
	'00001000' => '20',
	'00000800' => '21',
	'00000400' => '22',
	'00000200' => '23',
	'00000100' => '24');


// print_r($portsArr);



if(!empty($swip) && !empty($port) && !empty($userip)){
	$model = @snmpget($swip, 'public', "1.3.6.1.2.1.1.1.0");
	if(is_string($model))
	{
		if(stripos($model, 'DES-3526') == true) $model='DES-3526';
		if(stripos($model, 'DES-3200') == true) $model='DES-3200';
		if(stripos($model, 'DES-3528') == true) $model='DES-3528';
	}
	else
	{
		die('unknown model');	
	}
	checkPort($port);
	
	//Финальный результат.
	$resultMessage = "";

	$hexport = array_search($port, $portsArr);
	switch ($model) {
		case 'DES-3526':
			$acid = "21".$port;
			//Удаляем правило в 10 аксесе
			snmpset($swip, "private", "1.3.6.1.4.1.171.12.9.2.2.1.22.10.$acid", 'i' , "6");
			//Добавляем правило в 10 аксесе
			@snmpset($swip, "private", "1.3.6.1.4.1.171.12.9.2.2.1.4.10.$acid",'a' , "$userip");
			@snmpset($swip, "private", "1.3.6.1.4.1.171.12.9.2.2.1.5.10.$acid", 'a', '255.255.255.255');
			@snmpset($swip, "private", "1.3.6.1.4.1.171.12.9.2.2.1.20.10.$acid", 'i', '2');
			@snmpset($swip, "private", "1.3.6.1.4.1.171.12.9.2.2.1.21.10.$acid", 'x', "$hexport");
			$actionResult = @snmpset($swip, "private", "1.3.6.1.4.1.171.12.9.2.2.1.22.10.$acid", 'i', '4');
			if($actionResult){
				$resultMessage = "10 правило было успешно создано!";
			}else{
				$resultMessage = "Ошибка. 10 правило не было создано!";
			}
			break;
		case 'DES-3200':
			$acid = "21".$port;
			//Удаляем правило в 10 аксесе
			snmpset($swip, "private", "1.3.6.1.4.1.171.12.9.3.2.1.22.10.$acid", 'i' , "6");
			//Добавляем правило в 10 аксесе
			$snmp = "snmpset -v2c -c private $swip 1.3.6.1.4.1.171.12.9.3.2.1.4.10.$acid a $userip 1.3.6.1.4.1.171.12.9.3.2.1.20.10.$acid i 2 1.3.6.1.4.1.171.12.9.3.2.1.21.10.$acid x $hexport 1.3.6.1.4.1.171.12.9.3.2.1.22.10.$acid i 4";
			// echo $snmp;
			exec( $snmp, $actionResult );
			if(!empty($actionResult)){
				$resultMessage = "10 правило было успешно создано!";
			}else{
				$resultMessage = "Ошибка. 10 правило не было создано!";
			}
			break;
		case 'DES-3528':
			$acid = $port;
			//Удаляем правило в 10 аксесе
			snmpset($swip, "private", "1.3.6.1.4.1.171.12.9.3.2.1.22.10.$acid", 'i' , "6");
			//Добавляем правило в 10 аксесе
			$snmp = "snmpset -v2c -c private $swip 1.3.6.1.4.1.171.12.9.3.2.1.4.10.$acid a $userip 1.3.6.1.4.1.171.12.9.3.2.1.20.10.$acid i 2 1.3.6.1.4.1.171.12.9.3.2.1.21.10.$acid x $hexport 1.3.6.1.4.1.171.12.9.3.2.1.22.10.$acid i 4";
			exec( $snmp, $actionResult );
			if(!empty($actionResult)){
				$resultMessage = "10 правило было успешно создано!";
			}else{
				$resultMessage = "Ошибка. 10 правило не было создано!";
			}			
			break;
		default: $resultMessage = "Unknown model";
		break;
	}
}
// else{
// 	errorMsg("Вы не заполнили все поля!");
// }

//Выводим финальный результат
// echo $resultMessage;
?>

<h3><?=$resultMessage?></h3>