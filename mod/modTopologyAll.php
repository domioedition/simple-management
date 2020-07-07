<?php
//include '../inc/head.inc.php';
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$map = $_POST['map'];
}
?>


<div class="row">
<div class="col-lg-12">
<p>Для просмотра топологии выберите карту и кольцо. <a href="../index.php?page=nodes&action=update_all">Обновить БД</a></p>
</div>
</div>

<div class="row">
<div class="col-lg-2">

<form role="form" action="" method="post">
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
<option value=""></option>
<?php
if(isset($map)){
$ring = $_POST['ring'];
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
</form>
</div>

</div><!-- /.row -->


<?php

if(isset($map)&&isset($ring))
{
	$map = 'Lviv '. $map;
	$sql = "SELECT * FROM nodes WHERE sys_location='$map' AND sys_contact='$ring'";
	$result = mysqli_query($link, $sql);

foreach($result as $row){
	$ip = $row['ip_address'];
	$deviceStp = array();
	//Запрашиваем External Root Cost
	$ExternalRootCost  = @snmpget($ip, rcomm, ".1.3.6.1.4.1.171.12.15.2.3.1.14.0");
	if(!$ExternalRootCost)
	{
		$deviceStp[] = 	array(
							"ip"=>"$ip",
							"mac"=>"FALSE",
							"DesignatedBridge"=>"FALSE",
							"RootCost"=>"FALSE",
							"RootPort"=>"FALSE",
							"sys_name"=>"FALSE"
							);
	}
	else
	{	
	//Заменяем строку 'INTEGER:'
	$ExternalRootCost = str_replace('INTEGER: ','',$ExternalRootCost);
	//Проверяем четность полученного числа(отсюда будем знать количество шагов в цикле - переменная $step)
	if (($ExternalRootCost % 20000) == 0)
	{
	  $step = intval($ExternalRootCost/20000);
	}
	else
	{
	  $step = intval($ExternalRootCost/20000)+1;
	}

	//Regional Root Bridge - Запрашиваем мак_адресс свича
	$RegionalRootBridge = @snmpget($ip, rcomm, ".1.3.6.1.4.1.171.12.15.2.3.1.15.0");
	$RegionalRootBridge = substr($RegionalRootBridge,17,22);
	$RegionalRootBridge = substr($RegionalRootBridge,1,17);
	$RegionalRootBridge = str_replace(' ','-',$RegionalRootBridge);

	//Designated Root Bridge - Запрашиваем мак-адресс свича следующего по цепочке STP
	$DesignatedBridge = snmpget($ip, rcomm, ".1.3.6.1.4.1.171.12.15.2.3.1.17.0");
	$DesignatedBridge = substr($DesignatedBridge,17,22);
	$DesignatedBridge = substr($DesignatedBridge,1,17);
	$DesignatedBridge = str_replace(' ','-',$DesignatedBridge);
	
	//RootPort
	$RootPort = snmpget($row['ip_address'], rcomm, ".1.3.6.1.4.1.171.12.15.2.3.1.18.0");
	$RootPort = str_replace('INTEGER: ','',$RootPort);
	$RootPort = intval($RootPort);

	//Запрашиваем SysName
	$sys_name = snmpwalkoid($ip, rcomm, "1.3.6.1.2.1.1.5");
	$sys_name = str_replace("STRING: ","",implode(",", $sys_name));
	
	//Создаем массив, в котором будут находится свичи, первым вводим информацию о свиче который запрашивали
	$deviceStp[] =
			array(
			"ip"=>"$ip",
			"mac"=>"$RegionalRootBridge",
			"DesignatedBridge"=>$DesignatedBridge,
			"RootCost"=>$ExternalRootCost,
			"RootPort"=>$RootPort,
			"sys_name"=>$sys_name
			);
	}
	//цикл запроса к базе данных и построение дерева СТП
	
	for($i=1;$i<=$step;$i++){
		//ищем мак адресс свича на который строится наш текущий свич
		$sql = "SELECT * FROM nodes WHERE mac_address='$DesignatedBridge'";
		$result = mysqli_query($link, $sql);
		$row = mysqli_fetch_assoc($result);
		//Если mac адресс отсутствует в базе данных
		if($row == NULL){
			#echo error("MAC адресс не найден в базе. <a href=\"/device/update.php?map=$map&ring=$ring\">Update</a> базу данных.");
			#echo $ip;
			#exit;
		}
		//если в базе найдена запись с мак адрессом
		if($row != NULL)
		{
		//делаем запрос к свичу взятому из БД
		$DesignatedBridge = snmpget($row['ip_address'], rcomm, ".1.3.6.1.4.1.171.12.15.2.3.1.17.0");
		$DesignatedBridge = substr($DesignatedBridge,17,22);
		$DesignatedBridge = substr($DesignatedBridge,1,17);
		$DesignatedBridge = str_replace(' ','-',$DesignatedBridge);
		
		//External Root Cost
		$RootCost  = snmpget($row['ip_address'], rcomm, ".1.3.6.1.4.1.171.12.15.2.3.1.14.0");
		$RootCost = str_replace('INTEGER: ','',$RootCost);
		$RootCost = intval($RootCost);
		
		//RootPort
		$RootPort = snmpget($row['ip_address'], rcomm, ".1.3.6.1.4.1.171.12.15.2.3.1.18.0");
		$RootPort = str_replace('INTEGER: ','',$RootPort);
		$RootPort = intval($RootPort);
		
		$deviceStp[] = array(
							"ip"=>$row['ip_address'],
							"mac"=>$row['mac_address'],
							"DesignatedBridge"=>$DesignatedBridge,
							"RootCost"=>$RootCost,
							"RootPort"=>$RootPort,
							"sys_name"=>$row['sys_name']
							);
		//mysqli_free_result($result);
		}
	}
	
	$networkTopology["$ip"] = $deviceStp;

}

}
?>




<?php
/*Вывод топологии сети*/
if(isset($networkTopology) && is_array($networkTopology)){
echo "<div class=\"row\">
	<h4>$map - $ring</h4>
	</div>";
	foreach($networkTopology as $devices){
	echo '<pre>';
		foreach($devices as $node){
echo <<<LINE
$node[ip] - $node[mac] - $node[RootPort] - $node[RootCost] -> <br>
LINE;

		}
	echo '</pre>';	
	}
}?>

<?php
//include '../inc/foot.inc.php';
?>  
