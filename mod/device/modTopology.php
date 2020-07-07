<?php
//checkAccess(14);
//Запрашиваем External Root Cost (стоимость пусти)
$ExternalRootCost  = snmpget($ip, rcomm, ".1.3.6.1.4.1.171.12.15.2.3.1.14.0");
//Заменяем строку 'INTEGER:'
$ExternalRootCost = str_replace('INTEGER: ','',$ExternalRootCost);
//Проверяем четность полученного числа(отсюда будем згать количество шагов в цикле - переменная $step)
if (($ExternalRootCost % 20000) == 0)
{
  $step = intval($ExternalRootCost/20000);
}
else
{
  $step = intval($ExternalRootCost/20000)+1;
}
#echo $step;
//Regional Root Bridge - Запрашиваем мак_адресс свича
$RegionalRootBridge = @snmpget($ip, rcomm, ".1.3.6.1.4.1.171.12.15.2.3.1.15.0");
$RegionalRootBridge = substr($RegionalRootBridge,17,22);
$RegionalRootBridge = substr($RegionalRootBridge,1,17);
$RegionalRootBridge = str_replace(' ','-',$RegionalRootBridge);
#echo $RegionalRootBridge;

//Designated Root Bridge - Запрашиваем мак-адресс свича следующего по цепочке STP
$DesignatedBridge = snmpget($ip, rcomm, ".1.3.6.1.4.1.171.12.15.2.3.1.17.0");
$DesignatedBridge = substr($DesignatedBridge,17,22);
$DesignatedBridge = substr($DesignatedBridge,1,17);
$DesignatedBridge = str_replace(' ','-',$DesignatedBridge);
#echo $DesignatedBridge;

//Запрашиваем SysName
$sys_name = snmpwalkoid($ip, rcomm, "1.3.6.1.2.1.1.5");
$sys_name = str_replace("STRING: ","",implode(",", $sys_name));

//Создаем массив, в котором будут находится свичи, первым вводим информацию о свиче который запрашивали
$deviceSTP[0] = array(
				"ip"=>"$ip",
				"mac"=>"$RegionalRootBridge",
				"DesignatedBridge"=>$DesignatedBridge,
				"RootCost"=>$ExternalRootCost,
				"sys_name"=>$sys_name
				);
#print_r($deviceSTP);
//цикл запроса к базе данных и построение дерева СТП
for($i=1;$i<=$step;$i++){
	//ищем мак адресс свича на который строится наш текущий свич
	$sql = "select * from nodes where mac_address='$DesignatedBridge'";
	#$sql = "select * from device where mac_address='$DesignatedBridge'";
	$result = mysqli_query($link, $sql);
	$row = mysqli_fetch_assoc($result);

	//Если mac адресс отсутствует в базе данных
	if(!is_array($row)){
		#echo error("mac: $DesignatedBridge <br>адресс не найден в базе. <a href=\"/device/update.php?ip=$ip\">Update</a> базу данных. Если обновление базы данных не поможет, возможно в сеть доставили новое устройство.");
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
	
	$deviceSTP[] = array(
						"ip"=>$row['ip_address'],
						"mac"=>$row['mac_address'],
						"DesignatedBridge"=>$DesignatedBridge,
						"RootCost"=>$RootCost,
						"sys_name"=>$row['sys_name']
						);
	#mysqli_free_result($result);
	}
}
#mysqli_close($link);
/*Вывод информации о СТП дереве свичей*/
?>
<div class="row">
  <div class="col-lg-8">
	<?php
	foreach($deviceSTP as $device)
	{
echo <<<LINE
<pre>
<a href="../?ip=$device[ip]"><img src="/img/switch.png" align="right"></a>
<a href="../?ip=$device[ip]">$device[ip]</a>
SysName: $device[sys_name]
MAC: $device[mac]
RootCost: $device[RootCost]
</pre>
LINE;
	}
	?>
  </div>
</div><!-- /.row -->