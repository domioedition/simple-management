<?php
/*
	Access creator
*/
$strOut = '';
$ip_address = '';
$port = '';
$model = '';

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	//Получаем переменную part для того чтобы знать какую часть использовать.
	$part = $_POST['part'];
	
	//Если задана только одна айпишка, создаем 1 аксес.
	if($part == 1){
		$ip_address = $_POST['ip_address'];
		$port = $_POST['port'];
		$model = $_POST['model'];
		switch ($model){
			case 'DES-3526' : $strOut = "config access_profile profile_id 10 add access_id 21$port ip source_ip $ip_address destination_ip 255.255.255.255 port $port permit"; break;
			case 'DES-3200' : $strOut = "config access_profile profile_id 10  add access_id 21$port  ip  source_ip $ip_address port $port permit"; break;
			case 'DES-3528' : $strOut = "config access_profile profile_id 10  add access_id 21$port  ip  source_ip $ip_address port $port permit"; break;
		}		

	}
	
	//Если задана сеть и ни ужно сделать 24 аксеса
	if($part == 2){
		
		$ip_network = $_POST['ip_network'];
		$lastOctet = $_POST['lastOctet'];
		if(empty($ip_network))
			$ip_network = '10.200.130.0';
		$ip_range = explode('.', $ip_network);
		$ipArr = array();
		for($j=2; $j<=254;$j++)
		{
			$ipArr[] = $j;
		}
		switch($lastOctet){
			case '2-25'; $lastOctet=0; break;
			case '26-49'; $lastOctet=24; break;
			case '50-73'; $lastOctet=48; break;
			case '74-97'; $lastOctet=72; break;
			case '98-121'; $lastOctet=96; break;
			case '122-145'; $lastOctet=120; break;
			case '146-169'; $lastOctet=144; break;
			case '170-193'; $lastOctet=168; break;
			case '194-217'; $lastOctet=192; break;
			case '218-241'; $lastOctet=216; break;
		}
		$ipArr =  array_slice($ipArr, $lastOctet, 24);
		//$ip_str = '';
		for($i=1;$i<25;$i++){
		$ipTemp = $ip_range[0].'.'.$ip_range[1].'.'.$ip_range[2].'.'.$ipArr[$i-1];
$ip_str .= "\nconfig access_profile profile_id 10 add access_id $i ip source_ip $ipTemp port $i permit
config access_profile profile_id 20 add access_id $i ip source_ip 0.0.0.0 port $i deny\n";
		}
		
	}
}
?>

<div class="row">
<div class="col-lg-12">
	<p><small>Для создания 10-го правила достаточно ввести ip-адрес, номер порта и выбрать модель свича.</small></p>
</div>
<div class="col-lg-12">
<form role="form" action="" method="post">

<div class="form-group">
	<div class="col-lg-2">
	<input class="form-control" type="text" name="ip_address" value="<?=$ip_address?>" placeholder="ip-address"><br>
	<input class="form-control" type="text" name="port" value="<?=$port?>" placeholder="port"><br>
	<select class="form-control" name="model">
	<option disabled>Select Model</option>
	<option value=""></option>
	<option value="DES-3526">DES-3526</option>
	<option value="DES-3200">DES-3200</option>
	<option value="DES-3528">DES-3528</option>
	</select>
	<input type="text" name="part" value="1" hidden><br>
	<button type="submit" class="btn btn-primary">Create</button>	
	</div>
	<div class="col-lg-10">
	<textarea class="form-control" rows="5" name="text"><?=$strOut?></textarea>
	</div>
</div>

</form>

</div>
</div>

<h4>1-24</h4>
<div class="row">
<div class="col-lg-12">
	<p><small>Для создания целого списка аксесов для портов с 1 по 24.</small></p>
</div>
<div class="col-lg-12">
<form role="form" action="" method="post">

<div class="form-group">
	<div class="col-lg-2">
	<input class="form-control" type="text" name="ip_network" value="<?=$ip_network?>" placeholder="network"><br>
	<select class="form-control" name="lastOctet">
		<option disabled>ip range</option>
		<option value="2-25">2-25</option>
		<option value="26-49">26-49</option>
		<option value="50-73">50-73</option>
		<option value="74-97">74-97</option>
		<option value="98-121">98-121</option>
		<option value="122-145">122-145</option>
		<option value="146-169">146-169</option>
		<option value="170-193">170-193</option>
		<option value="194-217">194-217</option>
		<option value="218-241">218-241</option>
	</select>
	<input type="text" name="part" value="2" hidden><br>
	<button type="submit" class="btn btn-primary">Create</button>
	</div>
	<div class="col-lg-10">
		<textarea class="form-control" rows="20" name="text">
<?=$ip_str;?>
		</textarea>
	</div>
</div>

</form>

</div>
</div>
