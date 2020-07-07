<?php
include('../inc/conf.php');
include('../inc/functions.php');
$log = "Script runned succesfully. NOT updated:\n";
$city = 'LV';
if(isset($city)){
	$sql = "SELECT * FROM nodes WHERE city='$city'";
	//echo $sql;
	$result = mysqli_query($link, $sql) or die(mysqli_error());
	if(mysqli_num_rows($result)>0) {
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
						//successMsg("Node <a href=\"index.php?action=view&id=$id\">$ip_address</a> was succesfully updated.");
					} else{
						//errorMsg("Some error.");
						$log .= "$ip_address\n";
					}
				}
			}
		}
	}	
}

echo $log;
?>