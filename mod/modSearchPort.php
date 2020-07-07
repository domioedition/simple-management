<?php
//Проверка уровня доступа
if ($_SESSION['userAccess'] >= 14) {

//*******************************************************************************

if($_SERVER['REQUEST_METHOD'] == 'GET'){
	@$map = $_GET['map'];
	@$ring = $_GET['ring'];
	@$mac = $_GET['mac'];
	@$vlanTag = $_GET['vlanTag'];
}


// $t массив с кольцами и картами.
$t = array();
$t['Map2']['Ring1'] = 'Ring1';

?>

<div class="row">

<div class="col-lg-2">
	<form role="form" action="<?=$_SERVER['PHP_SELF']?>" method="get">
		<div class="form-group">
			<select class="form-control" name="map" onchange="this.form.submit()">
				<option disabled>Select Map</option>
				<option value=""></option>
				<?php
				foreach($t as $k=>$v){
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
		<div class="form-group">
			<select class="form-control" name="ring" onchange="this.form.submit()">
				<option disabled>Select Ring</option>
				<option value=""></option>";
				<?php
				if(isset($map)){
					$ring = $_GET['ring'];
				}else{
						unset($ring);
					}
				
				if (array_key_exists($map, $t))
				{
					foreach($t[$map] as $r){
						if($r != $ring)
						{
							echo "<option value=\"$r\">$r</option>";
						}
						else
						{
							echo "<option selected value=\"$r\">$r</option>";
						}
					}
				}
				?>
			</select>			
		</div>
</div>

<div class="col-lg-2">

<div class="form-group">
	<input class="form-control" name="mac" value="<?=$mac?>" placeholder="mac-address">
</div>
<div class="form-group">
	<input class="form-control" name="vlanTag" value="<?=$vlanTag?>" placeholder="vlan tag">
</div>

</div>

<div class="col-lg-3">
  <div class="form-group">
  </div>
  <button type="submit" class="btn btn-primary btn-lg">Search</button>
</form>
</div>

</div>
<!-- /.row -->



<div class="row">
<?php
if(isset($mac) && isset($vlanTag)){

//echo $map.'<br>';
//echo $ring.'<br>';
//echo $mac.'<br>';
//echo $vlanTag.'<br>';


/*Переводим MAC адресс сначала в массив,
	 потом в 16-ную систему, и после этого 
	 создаем переменную которую будем искатиь 
	 в ключах массива*/
	$searchMac = explode('-',$mac);
	$mac = '';
	foreach($searchMac as $part)
	{
		$x = hexdec($part);
		$mac .= $x.'.';	
	}
	$mac = substr($mac,0,strlen($mac)-1);
	//запись о мак адрессе которая будет искатся в массиве
	$searchMac = "SNMPv2-SMI::mib-2.17.7.1.2.2.1.2.$vlanTag.$mac";
	//echo $searchMac;
	

	
	$map = 'Lviv '.$map;
	//Запрос к базе данных в котором мы получим список ip-адресов
	$sql = "SELECT * FROM device WHERE sys_location='$map' and sys_contact='$ring'";
	$result = mysqli_query($link, $sql) or die(mysqli_error());
	if(mysqli_num_rows($result)>0){
		
 		while($row = $result->fetch_assoc()){
			//echo $row['ip_address'];
			$ip = $row['ip_address'];
			$getMacTable = @snmpwalkoid($ip, rcomm, "1.3.6.1.2.1.17.7.1.2.2.1.2.".$vlanTag, $timeout = 30000 ,$retries = 1);	//30000 - 30 сек
			//если мыполучаем массив со списком ip-адрессов, то производим в нем поиск.
			if(is_array($getMacTable)){
				if (array_key_exists($searchMac, $getMacTable))
				{
					if(($getMacTable[$searchMac] != 'INTEGER: 25')&&($getMacTable[$searchMac] != 'INTEGER: 26'))
					{
					$searchPort = str_replace('INTEGER: ', '', $getMacTable[$searchMac]);
					echo "<p>ip-address: $ip | port:$searchPort</p>";
					}
				}
			}
		}
		
	}
	
	
}
?>
</div>

<?php
} else {
	errorMsg("Извините у Вас нет прав на данное действие.");
}
?>