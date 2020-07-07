<?



// $lldp[] = '10.11.21.12';
// $lldp[] = '10.11.21.2';
// array_unshift($lldp,'10.11.21.11');
// $lldp[] = '10.11.21.8';
// array_unshift($lldp,'10.11.21.15');
// print_r($lldp);


/* мой тест для глобального построения сети по lldp*/

// function scanTest($ip_address){
// 	// global $request;
// 	$request = snmpwalkoid($ip_address, 'public', '1.0.8802.1.1.2.1.4.1.1.9',100000,2);
// 	if($request){		
// 		foreach ($request as $key => $value) {
// 			$port = explode('.', $key);
// 			preg_match("/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/", $value, $ip);
// 			if($ip[0] != ""){
// 				// echo $ip[0]."\n";
// 				//запускаем еще раз для того чтобы построить топоплогию
// 				// scanTest($ip[0]);
// 				//добавляем в массив
// 				// echo $ip[0].$port[12];
// 				$arr[$ip_address][$port[12]] = $ip[0];
// 				$newRequest = snmpwalkoid($ip[0], 'public', '1.0.8802.1.1.2.1.4.1.1.9',100000,2);
// 				foreach ($newRequest as $key => $value) {
// 					$port = explode('.', $key);
// 					preg_match("/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/", $value, $ip);
// 					if($ip[0] != ""){
// 						// echo $ip[0]."\n";
// 						$arr[$ip[0]][$port[12]] = $ip[0];
// 					}
// 				}
// 			// $neighborIP = '<a href="index.php?page=lldp&ip_address='.$ip[0].'">'.$ip[0].'</a>';
// 			// $neighborsReq[] = "$ip_address --->\t $port[12] port --->\t$neighborIP";
// 				// $neighborsReq[$ip[0]][] = $ip[0].'-'.$port[12];
// 				// $neighborsArr[] = $neighborsReq;
// 			}
// 		}

// 		// $allNeighbors[] = $arr;
// 	}
// 	print_r($arr);
// 	// echo $ip_address;
// }

// scanTest('10.11.21.12');





/*
	конец
*/


$ip_address = $_GET['ip_address'];


/*
// добавление всего пула
$sql = "select ip_address from nodes where sys_location='Lviv Map2' and sys_contact='Ring2' and city='LV'";
$result = $link->query($sql);
if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		add($row['ip_address']);
	}
}*/


//updating information
if(@$_GET['action'] == 'update'){
	echo "Updating...";
	$sql = "SELECT date FROM lldp WHERE ip_address='$ip_address' ORDER BY date LIMIT 0,1";
	$result = $link->query($sql);
	if ($result->num_rows > 0) {
		$row = $result->fetch_row();
		$timestamp = $row[0];
	}
	// print_r($timestamp);
	//1 - вариант
/*	$tempArray = array_count_values($timestamp);
	foreach ($tempArray as $key => $value) {
		$sql = "DELETE FROM lldp WHERE ip_address='$ip_address' AND date='$key'";
		if ($link->query($sql) === TRUE) {
			echo "Record deleted successfully \n";
		} else {
			echo "Error: " . $sql . "<br>" . $conn->error;
		}
		break;
	}*/

	//2 - вариант
	$sql = "DELETE FROM lldp WHERE ip_address='$ip_address' AND date='$timestamp'";
	if ($link->query($sql) === TRUE) {
		echo "Records deleted successfully <br>";
	} else {
		echo "Error: " . $sql . "<br>" . $link->error;
	}


	//Добавление новых данных
	foreach ($neighborsReq as $neighbor) {
		$date = time();
		$sql = "INSERT INTO lldp (ip_address,neighbors, date) VALUES ('$ip_address', '$neighbor', '$date')";
		if ($link->query($sql) === TRUE) {
			echo "New record created successfully \n";
		} else {
			echo "Error: " . $sql . "<br>" . $link->error;
		}
	}	
}


//Функция добавления в БД нового девайса
function add($ip_address){
	global $link;
	$request = snmpwalkoid($ip_address, 'public', '1.0.8802.1.1.2.1.4.1.1.9');
	if($request){
		foreach ($request as $key => $value) {
			$port = explode('.', $key);
			preg_match("/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/", $value, $ip); 
			if($ip[0] != ""){
				$neighbors = $ip['0'] ." - ".$port[12];
				$date = time();
				$sql = "INSERT INTO lldp (ip_address,neighbors, date) VALUES ('$ip_address', '$neighbors', '$date')";
				if ($link->query($sql) === TRUE) {
					echo "New record created successfully \n";
				} else {
					echo "Error: " . $sql . "<br>" . $conn->error;
				}
			}		
		}
	}
}

//Функция вывода старых записей из бд
function getInfoFromDB($ip_address){
	global $link;
	if(!empty($ip_address)){
		echo '<h3>Result from DB</h3>';
		$sql = "SELECT * FROM lldp WHERE ip_address='$ip_address' ORDER BY date DESC";
		$result = $link->query($sql);
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$date = date("Y-m-d H:i", $row['date']);
				$neighbor = explode('-', $row['neighbors']);
				$arr[$date][] = $ip_address.' -- '.$neighbor[1].' port --> '.$neighbor[0];
			}
			echo '<ul class="lldp_menu">';
			foreach ($arr as $dt => $str) {
				echo '<li><a href="#"><img src="../img/plus.png" alt="show"/>'.$dt.'</a><ul class="lldp_submenu">';
				//Сортируем данные(порты от 1 и по возростанию)
				natsort($str);
				foreach ($str as $record) {
					echo '<li>'.$record.'</li>';
				}
				echo '</ul></li>';
			}
			echo '</ul>';		
		} else {
			echo "<h4>0 results in DB.</h4>";
		}		
	}
}

//Сканируем свич и ищем в БД.
function scan($ip_address){
	global $link, $neighborsReq;
	$request = @snmpwalkoid($ip_address, 'public', '1.0.8802.1.1.2.1.4.1.1.9',100000,2);
	if($request){
		foreach ($request as $key => $value) {
			$port = explode('.', $key);
			preg_match("/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/", $value, $ip); 
			if($ip[0] != ""){
				$neighborIP = '<a href="index.php?page=lldp&ip_address='.$ip[0].'">'.$ip[0].'</a>';
				$neighborsReq[] = "$ip_address --->\t $port[12] port --->\t$neighborIP";
			}
		}
		natsort($neighborsReq);
		$sql = "SELECT * FROM lldp WHERE ip_address='$ip_address'";
		$result = $link->query($sql);
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$neighborsDB[] = $row['neighbors'];
			}
		} else {
			$neighborsDB[] = "";
		}
	//Вывод результата
		echo "<pre>";
		foreach ($neighborsReq as $neighbor) {
			echo "<p>$neighbor</p>";
		}
		echo "</pre>";
	} else {
		die("<h3>No response from <code>$ip_address</code>!</h3>");
	}
}
?>

<div class="row">
	<div class="col-lg-6">
		<div class="form-group">
			<form method="get" action="../index.php?page=lldp" role="form">
				<input type="text" name="page" value="lldp" hidden>
				<input class="form-control" type="text" placeholder="ip-address" name="ip_address" value="<?=$ip_address?>">
				<p class="help-block">enter the ip-address search neighbors using the lldp</p>
				<button type="submit" class="btn btn-primary">Show neighbors</button>
			</form>
		</div>
	</div>
</div>

<div class="row">
<div class="col-lg-12">


<?
if(!empty($ip_address)){
	echo "<h3>Current request</h3>";
	scan($ip_address);
	getInfoFromDB($ip_address);
}
?>


<?

?>

<?



?>
</div>
</div>