<?php
//Проверка уровня доступа
if ($_SESSION['userAccess'] >= 14) {

$defaultip = '10.10.131.185';
$defaultoid = "1.3.6.1.4.1.171.11.64.1.2.6.1.1.1";
$rcomm = "public";
if (isset($_POST['ip_address'])) $ip_address = $_POST['ip_address']; else $ip_address = $defaultip;
if (isset($_POST['oid'])) $oid = $_POST['oid']; else $oid = $defaultoid;
?>


<form role="form" action="" method="post">
<div class="form-group">
	<input class="form-control" name="ip_address" value="<?=$ip_address?>" placeholder="ip-address">
</div>
<div class="form-group">
	<input class="form-control" name="oid" value="<?=$oid?>" placeholder="oid">
</div>
<div class="form-group">
	<button type="submit" class="btn btn-primary">Check</button>
</div>
</form>
<?



function search($ip_address, $rcomm, $oid){
	$search = @snmpwalkoid($ip_address, $rcomm, $oid);
	echo "<pre>";
	print_r($search);
	echo "</pre>";
}
search($ip_address, $rcomm, $oid);

} else {
	errorMsg("Извините у Вас нет прав на данное действие.");
}
?>