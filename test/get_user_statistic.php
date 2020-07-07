<pre>
<?php

	require '../inc/functions.php';

	function macDecHex($p){
		global $result;
		$result = '';
		$m = explode(".",$p);
		foreach($m as $part){
			$x = dechex($part);
			if(strlen($x)<2) $x = '0'.$x;
			$result .= $x.':';			
		}
		$result = substr($result,0,-1);
		return $result;
	}

	function showMac($ip, $port, $model){
		$r = '';
		$vlanTag = snmpwalkoid($ip, rcomm, "1.3.6.1.2.1.17.7.1.4.5.1.1.".$port);
		$vlanTag = implode(",",$vlanTag);
		$vlanTag = str_replace('Gauge32: ', '', $vlanTag);
		$vlanName = snmpwalk($ip, rcomm, "1.3.6.1.2.1.17.7.1.4.3.1.1.".$vlanTag);
		$vlanName = implode(",",$vlanName);
		$vlanName = str_replace("\"","",str_replace('STRING: ', '', $vlanName));
		$macArr = snmpwalkoid($ip, rcomm, "1.3.6.1.2.1.17.7.1.2.2.1.2.".$vlanTag);
	// print_r($macArr);
		$tmpArr = array();
		if(is_array($macArr)){
			foreach($macArr as $key=>$mac){
				$searchMac = 'INTEGER: '.$port;
				if($mac == $searchMac){
					$mac = str_replace('SNMPv2-SMI::mib-2.17.7.1.2.2.1.2.'.$vlanTag.'.','',$key);
				// echo $mac."<br>";
					$tmpArr[] = $mac;
				}			
			}
		// echo count($tmpArr);
			if(count($tmpArr)>0){
				foreach($tmpArr as $item){
					$r .= $vlanTag."^".$vlanName."^".macDecHex($item)."^".$port."|";				
				}
			}else{
				$r .= $vlanTag."^".$vlanName."^"."no mac address"."^".$port."|";
			}
		}else{
			$r .= $vlanTag."^".$vlanName."^"."no mac address"."^".$port."|";
		}
		$r = substr($r,0,-1);
	// return $r;

		$result = explode("|", $r);

	// var_dump($result);
		$tr = "\n\n<tr>";
		$td = "";
		foreach ($result as $key => $row) {
			$item = explode("^", $row);
			foreach ($item as $key => $value) {
				$td .= "<td>$value</td>";
			}
			$tr .= $td."</tr>\n\n<tr>";
		}
		$tr .= "</tr>";

	// return $r;

	// if($macAddress == '00:'){$macAddress = 'no mac address';}else{$macAddress = substr($macAddress,0,strlen($macAddress)-1);}
		$macAddress = "
		<div class=\"table-responsive\">
			<table class=\"table\">
				<thead>
					<tr>
						<th>VID</th>
						<th>VLAN Name</th>
						<th>mac-address</th>
						<th>port</th>
					</tr>
				</thead>
				<tbody>$tr
				</tbody>
			</table>
		</div>";
		return $macAddress;
	}



	$ip = '10.11.21.11';

	getModel();

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

for ($i = 1; $i < 25; $i++){

$portState = substr($portAdminState[$i-1],8,9);
$pSpeed = substr($portNwayStatus[$i-1],8,9);

#Скорость линка на которой порт поднялся
if($model == 'DES-3526'){
	switch($pSpeed){
		// case 2: $pSpeed = '<button type="button" class="btn btn-default btn-xs">Link Down</button>'; break;
		case 3: $pSpeed = '<button type="button" class="btn btn-warning btn-xs">10Mbps-Half</button>';	break;
		case 4: $pSpeed = '<button type="button" class="btn btn-warning btn-xs">10Mbps-Full</button>'; break;
		case 5: $pSpeed = '<button type="button" class="btn btn-warning btn-xs">100Mbps-Half</button>'; break;	
		case 6: $pSpeed = '<button type="button" class="btn btn-success btn-xs">100Mbps-Full</button>'; break;
		default: $pSpeed = false;
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

if($pSpeed){

	echo  $i."\n";
	echo showMac($ip, $i, $model);
	// var_dump($pSpeed)."\n";
}

}//end FOR();






?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Users statistics</title>
</head>
<body>
	
</body>
</html>