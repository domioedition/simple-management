<?
session_start();
$start_time = microtime(true);
$login = $_SESSION['userLogin'];
include '../inc/conf.php';
include '../inc/functions.php';
include 'device.class.php';

checkAccess(14);

$ip = isset($_GET['ip']) ? $_GET['ip'] : false;
$port = isset($_GET['port']) ? $_GET['port'] : false;



if($ip){
	$device = new Device();
	$device->getModel($ip);
	$sysName = $device->getSysName($ip);
	$sysLocation = $device->getSysLocation($ip);
	$sysContact = $device->getSysContact($ip);	
}else{
	$sysName = "Device";
	$sysLocation = "";
	$sysContact = "";
}



include('../inc/head.inc.php');
?>
<div class="row">
  <div class="col-lg-12">
    <h3 class="page-header"><?=$sysName;?></h3>
  </div>
</div>

<div class="row">
	<div class="col-lg-6">
	<form role="form" method="get" accept="">
	<div class="form-group">
		<input id="ip" class="form-control" type="text" name="ip" value="<?=$ip?>" placeholder="Please enter ip-address">
	</div>
	<div class="form-group">
		<button type="submit" class="btn btn-default">Search</button>
	</div>
	</form>
	</div>
</div>

<?
if($ip or ($ip &&$port)){
?>
<div class="row">
	<div class="col-lg-12">
	<p><button id="showDeviceInfo" type="button" class="btn btn-primary btn-xs">show / hide Device Info</button></p>
	<div class="well well-sm" id="deviceInfo">
	
		<h4>Device</h4>
		<?
			echo "<p>$sysName $sysLocation $sysContact</p>";
		?>
			<button>ShowPorts</button>
			<button>Logs</button>
			<button>Topology</button>
			<button>ClearErrors</button>
			<button id="saveConfig" onclick="saveConfig()">Save</button>

	</div>	
	</div>
</div>
<?
}
?>

<div class="row">
<div class="col-lg-12">
<?
	if($ip && !$port){
		include 'portsTable.php';
	}
	if($port){
		include 'port.php';
	}
?>
</div>
<!-- /.col-lg-12 -->
</div>
<!-- /.row -->  




<div class="row">
<div class="col-lg-12">
	<?
	//Вывод времени выполнения скрипта
	$end_time = microtime(true);
	echo '<h4>'.round(($end_time-$start_time),3).' сек</h4>';
	?>

</div>
</div>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script type="text/javascript" src="js/xmlhttprequest.js"></script>
<script type="text/javascript" src="js/action.js"></script>
<script type="text/javascript" src="js/script.js"></script>

<?
include('../inc/foot.inc.php');
?>