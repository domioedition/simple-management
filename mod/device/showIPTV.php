<?php
//Модуль IPTV
$o = $_GET['o']; //o - operation(write, delete, etc.)
getModel();

//удаление ренджей с порта.
function deleteRange($model){
		checkAccess();
		global $ip, $port;
		switch($model){
			case 'DES-3526' :
				$nameRange = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.11.64.1.2.5.1.1.1");
				foreach($nameRange as $range){
					@snmpset($ip, wcomm, "1.3.6.1.4.1.171.11.64.1.2.5.2.2.1.5.$port.1", "i", 6);
				}
				$name = clearSTRING($nameRange[0]);
				exec ("snmpset -v2c -c private $ip 1.3.6.1.4.1.171.11.64.1.2.5.2.2.1.2.1.$port s $name 1.3.6.1.4.1.171.11.64.1.2.5.2.2.1.5.$port.1 i 4", $output);
				if(is_array($output) && count($output)>0){
					successMsg("Ranges removed!");
					$log = "Ranges removed";
					logSave($ip, $port, $log);
				}else{			
					$log = "Ranges not removed";
					logSave($ip, $port, $log);
					errorMsg("Ranges not removed!");
				}
			break;			
			case 'DES-3200' :
				$idRange = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.11.113.1.5.2.22.2.1.1");
				foreach($idRange as $range){
					$range = clearINTEGER($range);
					exec ("snmpset -v2c -c private $ip 1.3.6.1.4.1.171.11.113.1.5.2.22.3.1.2.$port i 3 1.3.6.1.4.1.171.11.113.1.5.2.22.3.1.3.$port i $range", $output);
				}
				exec ("snmpset -v2c -c private $ip 1.3.6.1.4.1.171.11.113.1.5.2.22.3.1.2.$port i 2 1.3.6.1.4.1.171.11.113.1.5.2.22.3.1.3.$port i 1", $output);
				if(is_array($output) && count($output)>0){
					successMsg("Ranges removed!");
					$log = "Ranges removed";
					logSave($ip, $port, $log);
				}else{			
					$log = "Ranges not removed";
					logSave($ip, $port, $log);
					errorMsg("Ranges not removed!");
				}
			break;
			case 'DES-3528' :
				$idRange = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.12.53.1.1.1");
				foreach($idRange as $range){
					$range = clearINTEGER($range);
					$snmp_request = @snmpset($ip, wcomm, "1.3.6.1.4.1.171.12.53.3.1.3.$port.$range", "i", 6);
				}
				$range = clearINTEGER($idRange[0]);
				$snmp_request = snmpset($ip, wcomm, "1.3.6.1.4.1.171.12.53.3.1.3.$port.$range", "i", 4);
				if($snmp_request){
					successMsg("Ranges removed!");
					$log = "Ranges removed";
					logSave($ip, $port, $log);
				}else{			
					$log = "Ranges not removed";
					logSave($ip, $port, $log);
					errorMsg("Ranges not removed!");
				}
			break;
			default : die("<h2>Unknown model.</h2>");
		}
}


//создание ренджей на порту.
function writeRange($model){
		global $ip, $port, $name;
		switch($model){
			case 'DES-3526' :
				$nameRange = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.11.64.1.2.5.1.1.1");
				foreach($nameRange as $range){
					$name = clearSTRING($range);
					exec ("snmpset -v2c -c private $ip 1.3.6.1.4.1.171.11.64.1.2.5.2.2.1.2.1.$port s $name 1.3.6.1.4.1.171.11.64.1.2.5.2.2.1.5.$port.1 i 4", $output);
				}
				if(is_array($output) && count($output)>0){
					$log = "Ranges created";
					logSave($ip, $port, $log);			
					successMsg("Ranges created!");
				}else{
					$log = "Ranges not created";
					logSave($ip, $port, $log);	
					errorMsg("Ranges not created!");
				}
			break;
			case 'DES-3200' :
				$idRange = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.11.113.1.5.2.22.2.1.1");
				foreach($idRange as $range){
					$range = clearINTEGER($range);
					exec ("snmpset -v2c -c private $ip 1.3.6.1.4.1.171.11.113.1.5.2.22.3.1.2.$port i 2 1.3.6.1.4.1.171.11.113.1.5.2.22.3.1.3.$port i $range", $output);					
				}
				if(is_array($output) && count($output)>0){
					$log = "Ranges created";
					logSave($ip, $port, $log);
					successMsg("Ranges were successful created!");
				}else{
					$log = "Ranges not created";
					logSave($ip, $port, $log);	
					errorMsg("Ranges not created!");
				}
			break;
			case 'DES-3528' :
				$idRange = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.12.53.1.1.1");
				foreach($idRange as $range){
					$range = clearINTEGER($range);
					$snmp_request = @snmpset($ip, wcomm, "1.3.6.1.4.1.171.12.53.3.1.3.$port.$range", "i", 4);
				}
				if($snmp_request){
					$log = "Ranges created";
					logSave($ip, $port, $log);
					successMsg("Ranges were successful created!");
				}else{
					$log = "Ranges not created";
					logSave($ip, $port, $log);	
					errorMsg("Ranges not created!");
				}				
			break;
			default : die("<h2>Unknown model.</h2>");
		}

}


//просмотр ренджей на порту.
function viewRange($model){
		global $ip, $port;
		switch($model){
			case 'DES-3526' : 
				$snmpNo = "1.3.6.1.4.1.171.11.64.1.2.5.2.2.1.1.$port";
				$snmpName = "1.3.6.1.4.1.171.11.64.1.2.5.2.2.1.2.$port";
				$snmpIpFrom = "1.3.6.1.4.1.171.11.64.1.2.5.2.2.1.3.$port";
				$snmpIpTo = "1.3.6.1.4.1.171.11.64.1.2.5.2.2.1.4.$port";
				//Это работает для моделей 3526
				echo "<table class=\"table table-hover\"><th>No.</th><th>Name</th><th>From</th><th>To</th>";
				$col1 = @snmpwalk($ip, rcomm, $snmpNo);
				$col2 = @snmpwalk($ip, rcomm, $snmpName);
				$col3 = @snmpwalk($ip, rcomm, $snmpIpFrom);
				$col4 = @snmpwalk($ip, rcomm, $snmpIpTo); 
				
				$fullTable = array('No.'=>$col1, 'Name'=>$col2, 'From'=>$col3, 'To'=>$col4);
				
				//print_r($fullTable);
				
				for($i=0; $i<count($fullTable["No."]); $i++){
					echo "<tr>
							<td>".clearINTEGER($fullTable["No."][$i])."</td>
							<td>".clearSTRING($fullTable["Name"][$i])."</td>
							<td>".clearIpAddress($fullTable["From"][$i])."</td>
							<td>".clearIpAddress($fullTable["To"][$i])."</td>
						</tr>";
				}
				echo "</table>";
			break;
			case 'DES-3200' : 
				$snmpNo = "1.3.6.1.4.1.171.11.113.1.5.2.22.5.1.2.$port";
				$snmpName = "1.3.6.1.4.1.171.11.113.1.5.2.22.2.1.2";
				$snmpIp = "1.3.6.1.4.1.171.11.113.1.5.2.22.2.1.4";
				$col1 = @snmpwalk($ip, rcomm, $snmpNo);
				$col2 = @snmpwalk($ip, rcomm, $snmpName);
				$col3 = @snmpwalk($ip, rcomm, $snmpIp);
				$col1 = clearSTRING($col1[0]);
				//print_r($col1);
				$col1 == "";
				if($col1 == ""){
					die("qq");
				}

				if($col1[0] != ""){
					if(strpos($col1,"-")){
						$col1 = explode("-",$col1);
						$a = $col1[0];
						$b = $col1[1];
						unset($col1);
						for($i=$a; $i<=$b; $i++){
							$col1[] = $i;
						}
						unset($a, $b);
					}elseif(strpos($col1,",")){
						$col1 = explode(",",$col1);
					}else{
						$col1 = explode(",",$col1);
					}
					$fullTable = array('No.'=>$col1, 'Name'=>$col2, 'FromTo'=>$col3);
					echo "<table class=\"table table-hover\"><th>No.</th><th>Name</th><th>From - To</th>";
					for($i=0; $i<count($fullTable["No."]); $i++){
						echo "<tr>
								<td>".$fullTable["No."][$i]."</td>
								<td>".clearSTRING($fullTable["Name"][$i])."</td>
								<td>".clearSTRING($fullTable["FromTo"][$i])."</td>
							</tr>";
					}
					echo "</table>";
				}
				else{
					errorMsg("No Ranges.");
				}
			break;
			case 'DES-3528' :
				$snmp_profile_id = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.12.53.1.1.1");
				$snmp_profile_name = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.12.53.1.1.2");
				$snmp_profile_name_on_port = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.12.53.3.1.2.$port");
				$snmp_ip_from = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.12.53.2.1.2");
				$snmp_ip_to = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.12.53.2.1.3");
				$fullTable = array(
							'id'=>$snmp_profile_id,
							'name'=>$snmp_profile_name,
							'profile_on_port'=>$snmp_profile_name_on_port,
							'ip_from'=>$snmp_ip_from,
							'ip_to'=>$snmp_ip_to
							);
				echo "<table class=\"table table-hover\"><th>No.</th><th>Name</th><th>From</th><th>To</th>";
				$count = count($fullTable['profile_on_port']);
				for($i=0; $i<$count; $i++){
					echo "<tr>
						<td>".clearINTEGER($fullTable["id"][$i])."</td>
						<td>".clearSTRING($fullTable["name"][$i])."</td>
						<td>".clearIpAddress($fullTable["ip_from"][$i])."</td>
						<td>".clearIpAddress($fullTable["ip_to"][$i])."</td>
					</tr>";
				}
				echo "</table>";
				//print_r($fullTable);
			break;
		default : die("<h2>Только модели DES-3526 и DES-3200.</h2>");
		}
		

}


?>
<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">IPTV</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
			<?php
			if($_SESSION["userAccess"]>=14){
			?>

				<a href="index.php?ip=<?=$ip?>&action=viewPort&port=<?=$port?>&mod=iptv"><button type="button" class="btn btn-primary btn-xs">viewRange</button></a>
				<a href="index.php?ip=<?=$ip?>&action=viewPort&port=<?=$port?>&mod=iptv&o=writeRange"><button type="button" class="btn btn-primary btn-xs">writeRange</button></a>
				<a href="index.php?ip=<?=$ip?>&action=viewPort&port=<?=$port?>&mod=iptv&o=deleteRange"><button type="button" class="btn btn-danger btn-xs">deleteRange</button></a>			


			<?php
			}else{
				?>
				<a href="index.php?ip=<?=$ip?>&action=viewPort&port=<?=$port?>&mod=iptv"><button type="button" class="btn btn-primary btn-xs">viewRange</button></a>
				<a href="index.php?ip=<?=$ip?>&action=viewPort&port=<?=$port?>&mod=iptv&o=writeRange"><button type="button" class="btn btn-primary btn-xs">writeRange</button></a>
				<?
			}
			?>
			</div>
			<div class="panel-body">
			<?php
			if($o == ""){
				viewRange($model);
			}

			if($o == "deleteRange"){
				deleteRange($model);
			}

			if($o == "writeRange"){
				writeRange($model);
			}
			?>
			</div>
		</div>
	</div>
</div>