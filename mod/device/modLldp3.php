<?php
//Функиця очистки строки от лишнего мусора.
function clearHex($str) {
	$str = str_replace("Hex-STRING: ","", $str);
	$str = substr($str,0, strlen($str)-1);
	$str = str_replace(" ", ":", $str);
	return $str;
}

function get_neigbors($ip){
	global $link, $full_table;
	$neigbors_mac = snmpwalk($ip, 'public', '1.0.8802.1.1.2.1.4.1.1.5');
	$neigbors_address = snmpwalk($ip, 'public', '1.0.8802.1.1.2.1.4.1.1.9');
	$neigbors = array_combine($neigbors_mac, $neigbors_address);
	//var_dump($neigbors);
	if($neigbors){
		foreach($neigbors as $k=>$v){
			$k = str_replace(":", "-", clearHex($k));
			$sql = "SELECT * FROM nodes WHERE mac_address='$k'";
			$result = $link->query($sql);
			if($result->num_rows > 0){
				while($row = $result->fetch_assoc()){
					$ip = $row["ip_address"];
					$address = $row["address"];
					$full_table[] = "<tr><td><a href=\"?page=lldp&ip_address=$ip\">$ip</a></td><td>$address</td><td>$v</td></tr>";
				}
			}else{
				$full_table[] = "<tr><td>Unknown node.</td><td>0</td></tr>";
			}
		}
	}
	return($full_table);
}

$ip_address = $_GET["ip_address"];
?>
<form action="../index.php?page=lldp" method="get">
<input type="text" name="page" value="lldp" hidden>
<input type="text" name="ip_address" placeholder="ip-address" value="<?=$ip_address?>">
<input type="submit" value="Show Neigbors">
</form>
<br>
<br>
<br>
<br>
<h3><?=$ip_address?> switch neigbors</h3>
<table class="table table-hover">
	<th>ip-address</th>
	<th>address</th>
	<th>snmp_address</th>
<?php
if(isset($ip_address)){
	get_neigbors($ip_address);
	foreach($full_table as $row){
		echo "$row";
	}
}
?>
</table>

<?

//ini_set('memory_limit', '-1'); 
/*
MAC Address       : 00-1E-58-9E-8B-B0
IP Address        : 10.11.22.2 (Manual)

мак коммутатора (из стп) в стандартном HEX-формате. 
snmpwalk -v2c -c public 10.10.40.102 1.3.6.1.2.1.17.1.1

мак свича (из) ллдп
snmpwalk -v2c -c private 10.10.40.102 1.0.8802.1.1.2.1.3.2.0

//vlan names
// 1.3.6.1.2.1.17.7.1.4.3.1.1
*/







/*
//Запись в Файл ip адрессов и маков.
for($i=250;$i<255;$i++){
	$ip_address = "10.11.22.$i";
	$mac = snmpwalk($ip_address, rcomm, '1.3.6.1.2.1.17.1.1');
	if($mac){
		$data = $ip_address."|".clearHex($mac[0])."\n";
		file_put_contents($_SERVER['DOCUMENT_ROOT']."/mod/device/lldp.txt", $data, FILE_APPEND);
	}
}
*/

/*
function get_children($ip, $id, $pid){
	global $table;
	$children = snmpwalk($ip, 'public', '1.0.8802.1.1.2.1.4.1.1.5');
	if($children){
		foreach($children as $k=>$child){
			$child_mac = clearHex($child);
			//var_dump($child_mac);
			//$child_ip = array_search($child_mac, $table);
			//var_dump($child_ip);
			//$full_table[$ip_start][]=$child_ip;
			//здесь нужно сделать поиск parrent_id
			for($i=0; $i<count($table); $i++){
				if($table[$i]["mac"] == $child_mac){
					$child_ip = $table[$i]["ip"];
					$child_id = $table[$i]["id"];
				}
			}
			// $full_table[] = array("id"=>$child_id, "pid"=>$pid, "ip"=>$child_ip);
			// $full_table[] = array("pid"=>$pid, "child_id"=>$child_id);
			$full_table[] = $child_ip;
		}
			// die("STOP FUNCTION");
	}
	return($full_table);
}









//Обьявляем массив table в котором буду ip адреса и mac.
$table = array();
$file = file($_SERVER['DOCUMENT_ROOT']."/mod/device/lldp.txt");
//Заполняем массив table.
for($i=0; $i<count($file); $i++){
	list($ip, $mac, $id) = explode('|',$file[$i]);
	//$table[$ip] = $mac;
	$id = substr($id,0, strlen($id)-1);
	$table[] = array("ip"=>$ip, "mac"=>$mac, "id"=>$id);
}
// var_dump($table);

for($i=0; $i<count($table); $i++){
	$x[$table[$i]["ip"]] = get_children($table[$i]["ip"], $i, $table[$i]["id"]);
}
//Исходный масств в котором содержатся соседи.
//print_r($x);	


function build_tree($ip, $arr){
	if(array_key_exists($ip, $arr)){
		$tree[$ip][] = $arr[$ip];		
	}
	print_r($tree);
	return $tree;
}


build_tree("10.11.22.254", $x);

print_r($tree);

*/








?>
