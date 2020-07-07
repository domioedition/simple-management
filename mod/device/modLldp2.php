<?php
//Функиця очистки строки от лишнего мусора.
function clearS($str){
	$str = str_replace("Hex-STRING: ","", $str);
	$str = substr($str,0, strlen($str)-1);
	$str = str_replace(" ", "-", $str);
	return $str;
}
//функция lldp.
function get_lldp($ip_address){
	global $link, $lldp;
	$request_mac = @snmpwalk($ip_address, 'public', '1.0.8802.1.1.2.1.4.1.1.5');
	$request_port = @snmpwalk($ip_address, 'public', '1.0.8802.1.1.2.1.4.1.1.8');
	foreach(array_combine($request_mac, $request_port) as $mac=>$port){
		$t = clearS($mac);
		$sql = "SELECT * FROM nodes WHERE mac_address='$t'";
		$result = mysqli_query($link, $sql);
		$row = mysqli_fetch_assoc($result);
		//print_r($row);
		$ip = $row['ip_address'];
		$mac = $row['mac_address'];
		$address = $row['address'];
		$lldp[$t]["ip"] = $ip;
		$lldp[$t]["mac"] = $mac;
		$lldp[$t]["address"] = $address;
		$lldp[$t]["port"] = clearSTRING($port);
	}
	return $lldp;
}
//Получаем ip-address.
$ip_address = $_GET['ip_address'];
//Получаем переменные
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$searchWord = $_POST['searchWord'];
	$city = $_POST['city'];
	$map = $_POST['map'];
	$ring = $_POST['ring'];
}
?>

<!-- Поиск по карте и по кольцу-->
<!--
<div class="row">
<div class="col-lg-12">
<form role="form" action="?page=lldp" method="post">
<div class="col-lg-2">
<select class="form-control" name="city" onchange="this.form.submit()">
<option disabled>Select City</option>
<option value=""></option>
<?php
foreach($cityMapRing as $k=>$v){
	if ($k != $city) { echo "<option value=\"$k\">$k</option>"; } else { echo "<option selected value=\"$k\">$k</option>"; }
}
?>
</select>
</div>

<div class="col-lg-2">
<select class="form-control"  name="map" onchange="this.form.submit()">
<option disabled>Select Map</option>
<option value=""></option>
<?php
foreach($cityMapRing[$city] as $k=>$v){
		if($k != $map){
			echo "<option value=\"$k\">$k</option>";
		}
		else{
			echo "<option selected value=\"$k\">$k</option>";
		}
}
?>
</select>
</div>

<div class="col-lg-2">
<select class="form-control"  name="ring" onchange="this.form.submit()">
<option disabled>Select Ring</option>
<option value=""></option>
<?php
foreach($cityMapRing[$city][$map] as $k=>$v){
		if($k != $ring)		{
			echo "<option value=\"$k\">$k</option>";
		}else{
			echo "<option selected value=\"$k\">$k</option>";
		}
}
?>
</select>
</div>
</form>


<div class="col-lg-2">
	<a href="../test/#/lldp" target="_blank"><button class="btn btn-primary btn-btn btn-primary">Графический вариант</button></a>
</div>

</div>
</div>
-->
<div class="row">
<div class="col-lg-12">
<form role="form" action="../index.php?page=lldp" method="get">
<div class="col-lg-2">
<input class="form-control" type="text" name="page" value="lldp" hidden>
<input class="form-control" type="text" name="ip_address" value="<?=$ip_address?>">
<button type="submit" class="btn btn-primary">Show LLDP</button>
</div>
</form>
</div>
</div>

<div class="row">
<div class="col-lg-12">
<?php
//Поиск по карте и по кольцу
if(!empty($city) && !empty($map) && !empty($ring)){
	switch($city){
		case 'LV' : $sys_location = 'Lviv '.$map; break;
	}

	//Созждаем строчку для файла lldp
	$str = '{"sys":{"repulsion":50,"friction":0.5,"stiffness":512,"gravity":true},"src":"{color:gray}\n\n';

	//запрос к БД.
	$sql = "SELECT * FROM nodes WHERE city='$city' AND sys_location='$sys_location' AND sys_contact='$ring' ORDER BY LENGTH(ip_address),ip_address";
	$result = mysqli_query($link, $sql) or die(mysqli_error());
	if(mysqli_num_rows($result)>0){
		while($row = $result->fetch_assoc()){
			while($row = $result->fetch_assoc()){
				$ip_address = $row['ip_address'];
				get_lldp($ip_address);
				foreach($lldp as $item){
					//print_r($item);
					$ip = $item['ip'];
					//echo "$ip_address -> $ip<br>";
					//Добавляем в строчку lldp ip адреса.
					$str .= "$ip_address -> $ip\\n";
					//die();
					//unset($item);
					unset($lldp);
					//echo $str;
				}
			}
		}
	}
	//Заканчиваем составление строчки
	$str .= '\n; endings\n'.$ip_address.' {color:green, shape:dot}\n","example":"lldp","title":"lldp"}';
	//Записываем строчку  в файл.
	$file = $_SERVER[DOCUMENT_ROOT].'/test/library/lldp.json';
	//var_dump($file);
	if(file_exists($file)){
		if($handle = fopen($file, "w+")){
			fwrite($handle, $str);
			fclose($handle);
		}
	}else{
		die("File not found!");
	}

}
?>
</div>
</div>
<!-- Конец Поиск по карте и по кольцу-->
<?php


/* //Созждаем строчку для файла lldp
$str = '{"sys":{"repulsion":50,"friction":0.5,"stiffness":512,"gravity":true},"src":"{color:gray}\n\n';

if(!empty($ip_address)&&empty($city)){
	echo "<a href=\"../test/#/lldp\" target=\"_blank\"><button class=\"btn btn-primary btn-lg\">Графический вариант</button></a>";
	echo "<h4>В коммутатор <code>$ip_address</code> включаются следующие коммутаторы:</h4>";
	get_lldp($ip_address);
echo "<table class=\"table table-hover\"><thead>
	<th>ip-address</th>
</thead>";
	foreach($lldp as $item){
		//print_r($item);
		$ip = $item['ip'];
		$port = $item['port'];
		//$address = $item['address'];
	echo "<tr>
			<td><a href=\"../index.php?page=lldp&ip_address=$ip\">$ip</a></td>
			<td>$port</td>
			<td>$address</td>
			<td>=></td>
			<td>Включается в порт коммутатора ?</td>
			<td><a href=\"../index.php?page=lldp&ip_address=$ip_address\">$ip_address</a></td>
		</tr>";
		//Добавляем в строчку lldp ip адреса.
		$str .= "$ip -> $ip_address\\n";
	}
}else{
	echo '<h4>Enter ip-address</h4>';
}
echo "</table>";

//Заканчиваем составление строчки
$str .= '\n; endings\n'.$ip_address.' {color:green, shape:dot}\n","example":"lldp","title":"lldp"}';
//Записываем строчку  в файл.
$file = $_SERVER[DOCUMENT_ROOT].'/test/library/lldp.json';
//var_dump($file);
if(file_exists($file)){
	if($handle = fopen($file, "w+")){
		fwrite($handle, $str);
		fclose($handle);
	}
}else{
	die("File not found!");
} */


?>



