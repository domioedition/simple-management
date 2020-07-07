<?php
if($_SERVER['REQUEST_METHOD'] == 'POST'){
	//$ip = $_GET['ip'];
	//$action = $_GET['action'];
	$map = $_POST['map'];
}
// $t массив с кольцами и картами.
$t = array();
$t['Map1']['Ring1'] = 'Ring1';





?>

<div class="col-lg-2">
<p>Select Map and Ring</p>
<form role="form" action="?page=getModels" method="post">
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

<br><br><br><br><br><br><br><br>


<pre>
<?php


	if(!empty($map) && !empty($ring)){
		$map = 'Lviv '.$map;
		$sql = "SELECT id, ip_address FROM nodes WHERE sys_location='$map' and sys_contact='$ring'";
		
		$result = mysqli_query($link, $sql) or die(mysqli_error());//запрос к БД.
		if(mysqli_num_rows($result)>0){
			
			while($row = $result->fetch_assoc()){
				$ip = $row['ip_address'];
				$model = @snmpget($ip, rcomm, "1.3.6.1.2.1.1.1.0");
				if(is_string($model))
				{
					if(stripos($model, 'DES-3526') == true) $model='DES-3526 ';
					if(stripos($model, 'DES-3200') == true) $model='DES-3200 ';
					if(stripos($model, 'DES-3528') == true) $model='DES-3528 ';
				}
				$arr[] = $model.$ip;
				
			}
			
		}
		natsort($arr);
		print_r($arr);
	}

?>

</pre>
<?






?>