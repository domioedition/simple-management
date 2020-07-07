<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$searchWord = $_POST['searchWord'];
	$city = $_POST['city'];
	$map = $_POST['map'];
	$ring = $_POST['ring'];
}




?>


<div class="row">
	<div class="col-lg-6">
		<form role="form" action="" method="post">
			<div class="form-group">
			<input class="form-control" name="searchWord" value="<?=$searchWord?>" placeholder="Что ищем? ip-address, mac-address, просто адрес...">
			<br>
			<button type="submit" class="btn btn-primary">Поиск</button>
			</div>
		</form>
	</div>
	
	

	
	<form role="form" action="?page=nodes" method="post">
	
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
	foreach($cityMapRing[$city] as $k=>$v) {
			if($k != $map)
			{
				echo "<option value=\"$k\">$k</option>";
			}
			else
			{
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
	foreach($cityMapRing[$city][$map] as $k=>$v) {
			if($k != $ring)
			{
				echo "<option value=\"$k\">$k</option>";
			}
			else
			{
				echo "<option selected value=\"$k\">$k</option>";
			}
	}
	?>
	</select>
	</div>


	</form>

	
</div>



<div class="row">
<div class="col-lg-12">

<?php




if (isset($searchWord) && $searchWord!='') {
	if (preg_match("/((25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(25[0-5]|2[0-4]\d|[01]?\d\d?)/", $searchWord)) {
		$sql = "SELECT * FROM nodes WHERE ip_address LIKE '%$searchWord%' ORDER BY LENGTH(ip_address),ip_address";
	} elseif(preg_match("/([0-9a-fA-F]{2}([:-]|$)){6}$|([0-9a-fA-F]{4}([.]|$)){3}/", $searchWord)) {
		$sql = "SELECT * FROM nodes WHERE mac_address='$searchWord'";
	} elseif(preg_match("/ /", $searchWord)) {
		$fullAddress = explode(" ", $searchWord);
		$sql = "SELECT * FROM nodes WHERE address LIKE '%$fullAddress[0]%' AND address LIKE '%$fullAddress[1]%' AND address LIKE '%$fullAddress[2]%'";
	} else {
		$sql = "SELECT * FROM nodes WHERE address LIKE '%$searchWord%' ORDER BY LENGTH(address),address";
	}
} elseif (!empty($city) && !empty($map) && !empty($ring)) {
	switch($city){
		case 'LV' : $sys_location = 'Lviv '.$map; break;
	}
	$sql = "SELECT * FROM nodes WHERE city='$city' AND sys_location='$sys_location' AND sys_contact='$ring' ORDER BY LENGTH(ip_address),ip_address";
} else {
	echo "<h3>Заполните поле поиска.</h3>";
}

	
if(isset($searchWord) && $searchWord!='' or (!empty($city) && !empty($map) && !empty($ring))) {
	$result = mysqli_query($link, $sql) or die(mysqli_error());//запрос к БД.
	if(mysqli_num_rows($result)>0) {
		$row_cnt = $result->num_rows;
		echo "<small><p>Колличество записей: $row_cnt</p>
		<p>Для обновления данных в БД надо выбрать город, карту, кольцо и нажать <a href=\"index.php?page=nodes&action=update&city=LV&map=Lviv $map&ring=$ring\">Click to Update</a>.</p></small>";
	?>
	<div class="panel panel-default">
	<div class="panel-body">
	<div class="table-responsive">
	<table class="table table-hover">
	<thead>
	<tr>
	<th>city</th>
	<th>ip-address</th>
	<th>model</th>
	<th>address</th>
	<th>&nbsp;</th>
	<th>&nbsp;</th>
	</tr>
	</thead>
	<tbody>	
	<?php
	while($row = $result->fetch_assoc())
	{
	$ip = $row['ip_address'];
	$model = @snmpget($ip, rcomm, "1.3.6.1.2.1.1.1.0");
	if(is_string($model))
	{
		if(stripos($model, 'DES-3526') == true) $model='DES-3526 ';
		if(stripos($model, 'DES-3200') == true) $model='DES-3200 ';
		if(stripos($model, 'DES-3528') == true) $model='DES-3528 ';
		if(stripos($model, 'DGS-3612G') == true) $model='DGS-3612G ';
		if(stripos($model, 'DGS-3627G') == true) $model='DGS-3627G ';
	}
	echo "<tr>
		<td>$row[city]</td>
		<td>$row[ip_address]</td>
		<td>$model</td>
		<td>$row[address]</td>
		<td><a href=\"../index.php?ip=$row[ip_address]&mod=topology\">Топология</a></td>
		<td><a href=\"../index.php?ip=$row[ip_address]\">Управление</a></td>
		</tr>";
	}
	?>
	</tbody>
	</table>
	</div>
	</div>
	</div>
	<?php
	
	}
	else {
		echo "<h3>Запись <code>$searchWord</code> не найдена!</h3>";
	}
}




//методы обновления, удаления и редактирования записей в БД
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$action = $_GET['action'];
	$city = $_GET['city'];
	$map = $_GET['map'];
	$ring = $_GET['ring'];
	if ($action == 'update') {
		
		//Проверка уровня доступа
		if ($_SESSION['userAccess'] >= 14) {
		
		if(isset($map) && isset($ring)){
			$sql = "SELECT * FROM nodes WHERE city='$city' and sys_location='$map' and sys_contact='$ring'";
			//echo $sql;
			$result = mysqli_query($link, $sql) or die(mysqli_error());
			if (mysqli_num_rows($result)>0) {
				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];					
					$ip_address = $row['ip_address'];
					$mac_address = @snmpget($ip_address, rcomm, ".1.3.6.1.4.1.171.12.15.2.3.1.15.0");
					if($mac_address){
						$mac_address = substr($mac_address,17,22);
						$mac_address = substr($mac_address,1,17);
						$mac_address = str_replace(' ','-',$mac_address);
						
						$mac_address_db = $row['mac_address'];
						$sys_name_db = $row['sys_name'];
						$sys_location_db = $row['sys_location'];
						$sys_contact_db = $row['sys_contact']; 
						
						$sys_name = snmpwalkoid($ip_address, rcomm, "1.3.6.1.2.1.1.5");//Запрос sysName
						$sys_name = str_replace("STRING: ","",implode(",", $sys_name));
						$sys_location = snmpwalkoid($ip_address, rcomm, "1.3.6.1.2.1.1.6");//Запрос sysLocation (карта)
						$sys_location = str_replace("STRING: ","",implode(",", $sys_location));
						$sys_contact = snmpwalkoid($ip_address, rcomm, "1.3.6.1.2.1.1.4");//Запрос sysContact  (кольцо)
						$sys_contact = str_replace("STRING: ","",implode(",", $sys_contact));
						//проверка на различие в БД и полученных данных
						if(($mac_address != $mac_address_db) or ($sys_name_db != $sys_name) or ($sys_location != $sys_location) or ($sys_contact != $sys_contact)){
							
							//sql запрос
							$sql = "UPDATE nodes SET city='$city', mac_address='$mac_address', sys_name='$sys_name', sys_location='$sys_location', sys_contact='$sys_contact' WHERE ip_address='$ip_address'";
							echo $sql;
							if (mysqli_query($link, $sql)) {
								successMsg("Node <a href=\"index.php?action=view&id=$id\">$ip_address</a> was succesfully updated.");
							} else{
								errorMsg("Some error.");
							}
						}
					}
				}
				errorMsg("Nothing to update.");
			}else{
				errorMsg("Unknown $map & $ring");//вывод сообщения об ошибке если не найдены map и ring
			}	
		}
		} else {
			errorMsg("Извините у Вас нет прав на данное действие.");			
		}
	}
}



//методы обновления всех записей $city='LV'
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
	$action = $_GET['action'];
	$city = 'LV';
	if ($action == 'update_all') {
		//Проверка уровня доступа
		if ($_SESSION['userAccess'] >= 14) {
		if(isset($city)){
			$sql = "SELECT * FROM nodes WHERE city='$city'";
			//echo $sql;
			$result = mysqli_query($link, $sql) or die(mysqli_error());
			if (mysqli_num_rows($result)>0) {
				while($row = mysqli_fetch_assoc($result)){
					$id = $row['id'];					
					$ip_address = $row['ip_address'];
					$mac_address = @snmpget($ip_address, rcomm, ".1.3.6.1.4.1.171.12.15.2.3.1.15.0");
					if($mac_address){
						$mac_address = substr($mac_address,17,22);
						$mac_address = substr($mac_address,1,17);
						$mac_address = str_replace(' ','-',$mac_address);
						
						$mac_address_db = $row['mac_address'];
						$sys_name_db = $row['sys_name'];
						$sys_location_db = $row['sys_location'];
						$sys_contact_db = $row['sys_contact']; 
						
						$sys_name = snmpwalkoid($ip_address, rcomm, "1.3.6.1.2.1.1.5");//Запрос sysName
						$sys_name = str_replace("STRING: ","",implode(",", $sys_name));
						$sys_location = snmpwalkoid($ip_address, rcomm, "1.3.6.1.2.1.1.6");//Запрос sysLocation (карта)
						$sys_location = str_replace("STRING: ","",implode(",", $sys_location));
						$sys_contact = snmpwalkoid($ip_address, rcomm, "1.3.6.1.2.1.1.4");//Запрос sysContact  (кольцо)
						$sys_contact = str_replace("STRING: ","",implode(",", $sys_contact));
						//проверка на различие в БД и полученных данных
						if(($mac_address != $mac_address_db) or ($sys_name_db != $sys_name) or ($sys_location != $sys_location) or ($sys_contact != $sys_contact)){
							
							//sql запрос
							$sql = "UPDATE nodes SET city='$city', mac_address='$mac_address', sys_name='$sys_name', sys_location='$sys_location', sys_contact='$sys_contact' WHERE ip_address='$ip_address'";
							//echo $sql;
							if (mysqli_query($link, $sql)) {
								successMsg("Node <a href=\"index.php?action=view&id=$id\">$ip_address</a> was succesfully updated.");
							} else{
								errorMsg("Some error.");
							}
						}
					}
				}
				errorMsg("Nothing to update.");
			}else{
				errorMsg("Unknown $map & $ring");//вывод сообщения об ошибке если не найдены map и ring
			}	
		}
		} else {
			errorMsg("Извините у Вас нет прав на данное действие.");			
		}
	}
}




?>

</div>
</div>
