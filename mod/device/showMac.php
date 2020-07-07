<?
session_start();
include('../../inc/conf.php');
include('../../inc/functions.php');

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
if($_SERVER['REQUEST_METHOD'] == "POST"){
	$ip = $_POST['ip'];
	$port = $_POST['port'];
	getModel();
	echo showMac($ip, $port, $model);
	$log = "Show mac-address";
	logSave($ip, $port, $log);
}else{
	Error(1);
}
?>