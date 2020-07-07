<?
session_start();
include('../inc/conf.php');
include('../inc/functions.php');


//функция получения данных со свича о состояни порта. Его статус выставленная скокрость, скорость на которой он поднялся.
function showPort($ip, $port, $model, $comm='public')
{
	/*Отправляем запрос на получение данных со свича*/
	switch($model){
	case 'DES-3526':
		//Админ. статус порта
		$portAdminState = snmpwalk($ip, $comm, "1.3.6.1.4.1.171.11.64.1.2.4.2.1.3.$port");
		//Выставленная скорость на порту
		$portCtrlNwayState = snmpwalk($ip, $comm, "1.3.6.1.4.1.171.11.64.1.2.4.2.1.4.$port");
		//Скорость линка на которой порт поднялся
		$portNwayStatus = snmpwalk($ip, $comm, "1.3.6.1.4.1.171.11.64.1.2.4.1.1.5.$port");					
		break;
	case 'DES-3200':
		$portAdminState = snmpwalk($ip, $comm, "1.3.6.1.4.1.171.11.113.1.5.2.2.2.1.3.$port");
		$portCtrlNwayState = snmpwalk($ip, $comm, "1.3.6.1.4.1.171.11.113.1.5.2.2.2.1.4.$port");
		$portNwayStatus = snmpwalk($ip, $comm, "1.3.6.1.4.1.171.11.113.1.5.2.2.1.1.5.$port");
		break;
	case 'DES-3528':
		$portAdminState = snmpwalk($ip, $comm, "1.3.6.1.4.1.171.11.105.1.2.3.2.1.4.$port.1");
		$portCtrlNwayState = snmpwalk($ip, $comm, "1.3.6.1.4.1.171.11.105.1.2.3.2.1.5.$port.1");
		$portNwayStatus = snmpwalk($ip, $comm, "1.3.6.1.4.1.171.11.105.1.2.3.1.1.6.$port.1");
		break;
	}
	/*
		Выбираем из массива последнюю цифру, и приводим переменную к типу string
	*/	
		$portAdminState = substr($portAdminState[0],8,9);
		$portNwayStatus = substr($portNwayStatus[0],8,9);
		$portCtrlNwayState = substr($portCtrlNwayState[0],8,9);
	/*
		Определяем статус порта
	*/
		switch($portAdminState){
			case 2: $portAdminState = 'Disable'; break;
			case 3: $portAdminState = 'Enable'; break;
		}
	/*
		Определяем выставленную скорость на порту
	*/
		if($model == 'DES-3526'){
			switch($portCtrlNwayState){
				case 2: $portCtrlNwayState = 'Auto'; break;
				case 3: $portCtrlNwayState = '10Mbps-Half'; break;
				case 4: $portCtrlNwayState = '10Mbps-Full'; break;
				case 5: $portCtrlNwayState = '100Mbps-Half'; break;
				case 6: $portCtrlNwayState = '100Mbps-Full'; break;
				case 7: $portCtrlNwayState = '1Gigabps-Half'; break;
				case 8: $portCtrlNwayState = '1Gigabps-Full'; break;
			}
		}
		if($model == 'DES-3200'){
			switch($portCtrlNwayState){
				case 1: $portCtrlNwayState = 'Auto'; break;
				case 2: $portCtrlNwayState = '10Mbps-Half'; break;
				case 3: $portCtrlNwayState = '10Mbps-Full'; break;
				case 4: $portCtrlNwayState = '100Mbps-Half'; break;
				case 5: $portCtrlNwayState = '100Mbps-Full'; break;
				case 6: $portCtrlNwayState = '1Gigabps-Half'; break;
				case 7: $portCtrlNwayState = '1Gigabps-Full'; break;
			}
		}
		if($model == 'DES-3528'){
			switch($portCtrlNwayState){
				case 2: $portCtrlNwayState = 'Auto'; break;
				case 3: $portCtrlNwayState = '10Mbps-Half'; break;
				case 4: $portCtrlNwayState = '10Mbps-Full'; break;
				case 5: $portCtrlNwayState = '100Mbps-Half'; break;
				case 6: $portCtrlNwayState = '100Mbps-Full'; break;
				case 7: $portCtrlNwayState = '1Gigabps-Half'; break;
				case 8: $portCtrlNwayState = '1Gigabps-Full'; break;
			}
		}
	/*
		Определяем скорость на которой линк поднялся
	*/
		
		if($model == 'DES-3526'){
			switch($portNwayStatus){
				case 2: $portNwayStatus = '<button type="button" class="btn btn-default btn-sm">Link Down</button>'; break;
				case 3: $portNwayStatus = '<button type="button" class="btn btn-warning btn-sm">10Mbps-Half</button>';	break;
				case 4: $portNwayStatus = '<button type="button" class="btn btn-warning btn-sm">10Mbps-Full</button>'; break;
				case 5: $portNwayStatus = '<button type="button" class="btn btn-warning btn-sm">100Mbps-Half</button>'; break;	
				case 6: $portNwayStatus = '<button type="button" class="btn btn-success btn-sm">100Mbps-Full</button>'; break;
			}
		}
		if($model == 'DES-3200'){
			switch($portNwayStatus){
				case 1: $portNwayStatus = '<button type="button" class="btn btn-default">Link Down</button>'; break;
				case 2: $portNwayStatus = '<button type="button" class="btn btn-warning">10Mbps-Half</button>'; break;
				case 3: $portNwayStatus = '<button type="button" class="btn btn-warning">10Mbps-Full</button>'; break;
				case 4: $portNwayStatus = '<button type="button" class="btn btn-warning">100Mbps-Half</button>'; break;
				case 5: $portNwayStatus = '<button type="button" class="btn btn-success">100Mbps-Full</button>'; break;
			}
		}
		if($model == 'DES-3528'){
			switch($portNwayStatus){
				case 0: $portNwayStatus = '<button type="button" class="btn btn-default">Link Down</button>'; break;
				case 4: $portNwayStatus = '<button type="button" class="btn btn-warning">10Mbps-Half</button>';	break;
				case 2: $portNwayStatus = '<button type="button" class="btn btn-warning">10Mbps-Full</button>'; break;
				case 8: $portNwayStatus = '<button type="button" class="btn btn-warning">100Mbps-Half</button>'; break;
				case 6: $portNwayStatus = '<button type="button" class="btn btn-success">100Mbps-Full</button>'; break;
			}
		}
//склеиваем все полученное и возвращаем результат
$result = "
<div class=\"table-responsive\">
<table class=\"table\">
    <thead>
        <tr>
			<th>Port</th>
			<th>State</th>
			<th>Settings<br>Speed/Duplex</th>
			<th>Connection<br>Speed/Duplex</th>
        </tr>
    </thead>
    <tbody>
        <tr>
			<td>$port</td>
			<td>$portAdminState</td>
			<td>$portCtrlNwayState</td>
			<td>$portNwayStatus</td>
        </tr>
    </tbody>
</table>
</div>";

	return $result;
}



if($_SERVER['REQUEST_METHOD'] == "POST"){
	$ip = $_POST['ip'];
	$port = $_POST['port'];
	getModel();
	echo showPort($ip, $port, $model);
	sleep(1);
}else{
	Error(1);
}
// echo showPort('10.11.21.11', '1', 'DES-3526');
// echo showPort($ip, $port, $model);
?>