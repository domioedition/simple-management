<pre>
<?php
/* include('../../inc/conf.php');
error_reporting(E_ALL & ~E_NOTICE);
$ip = '10.11.62.2';

$branch1[0] = array("ip"=>"$ip",
					"mac"=>""
					);
// 1 свич
$lldpMac = snmpwalk($ip, 'private', '1.0.8802.1.1.2.1.4.1.1.5');
$macArr = array();
foreach($lldpMac as $item){
	$item = str_replace('Hex-STRING: ', '', $item);
	$item = str_replace(' ', '-', $item);
	$item = substr($item, 0, -1);
	$macArr[] = $item;
}
print_r($macArr);
$sql = "select * from device where mac_address='$macArr[0]'";
$result = mysqli_query($link, $sql);
$row = mysqli_fetch_assoc($result);
$branch1[] = array("ip"=>"$row[ip_address]",
					"mac"=>"$row[mac_address]"
					);
//print_r($branch1);
// 2 свич
$lldpMac = snmpwalk($row['ip_address'], 'private', '1.0.8802.1.1.2.1.4.1.1.5');
$macArr = array();
foreach($lldpMac as $item){
	$item = str_replace('Hex-STRING: ', '', $item);
	$item = str_replace(' ', '-', $item);
	$item = substr($item, 0, -1);
	$macArr[] = $item;
}
print_r($macArr);
$sql = "select * from device where mac_address='$macArr[0]'";
$result = mysqli_query($link, $sql);
$row = mysqli_fetch_assoc($result);
$branch1[] = array("ip"=>"$row[ip_address]",
					"mac"=>"$row[mac_address]"
					);
//print_r($branch1);

// 3 свич
$lldpMac = snmpwalk($row['ip_address'], 'private', '1.0.8802.1.1.2.1.4.1.1.5');
$macArr = array();
foreach($lldpMac as $item){
	$item = str_replace('Hex-STRING: ', '', $item);
	$item = str_replace(' ', '-', $item);
	$item = substr($item, 0, -1);
	$macArr[] = $item;
}
print_r($macArr);
$sql = "select * from device where mac_address='$macArr[0]'";
$result = mysqli_query($link, $sql);
$row = mysqli_fetch_assoc($result);
$branch1[] = array("ip"=>"$row[ip_address]",
					"mac"=>"$row[mac_address]"
					);

					
					
print_r($branch1);




/*
$lldpMac = snmpwalk($ip, 'private', '1.0.8802.1.1.2.1.4.1.1.5');

$macArr = array();
foreach($lldpMac as $item){
	$item = str_replace('Hex-STRING: ', '', $item);
	$item = str_replace(' ', '-', $item);
	$item = substr($item, 0, -1);
	$macArr[] = $item;
}

for($i=1; $i<7; $i++){
	$sql = "select * from device where mac_address='$macArr[0]'";
	$result = mysqli_query($link, $sql);
	$row = mysqli_fetch_assoc($result);
	
	
	$snmpRequest = snmpwalk($row['ip_address'], 'private', '1.0.8802.1.1.2.1.4.1.1.5');
	
	
	$branch1[] = array(
					"ip"=>$row['ip_address'],
					"mac"=>$row['mac_address']
					);
	

					
					
					
					
}

*/

 */










?>