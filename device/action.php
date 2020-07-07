<?
session_start();
$login = $_SESSION['userLogin'];
include '../inc/conf.php';
include '../inc/functions.php';
include 'device.class.php';
checkAccess(14);

if($_SERVER['REQUEST_METHOD'] == 'POST'){

$ip = $_POST['ip'];
@$port = $_POST['port'];
$action = $_POST['action'];



	$device = new Device();

	switch ($action) {
		case 'saveConfig':
			$device->saveConfig($ip, 'private');
			break;
		case 'reconnect':
			$device->portReconnect($ip, $port, 'private');
			break;
		case 'enable':
			$device->portEnable($ip, $port, 'private');
			break;
		case 'disable':
			$device->portDisable($ip, $port, 'private');
			break;
		case 'auto':
			$device->portAuto($ip, $port, 'private');
			break;
		case '100':
			$device->port100($ip, $port, 'private');
			break;
		case '10':
			$device->port10($ip, $port, 'private');
			break;
		case 'showPortError':
			$device->showErrors($ip, $port);
			break;
		case 'showCableDiag':
			$device->showCableDiag($ip, $port, 'private');
			break;
		case 'showMacAddress':
			$device->showMacAddress($ip, $port);
			break;
	}

}else{
	Error(1);
}//end method POST




// $ip = '10.11.21.12';
// $port = 1;
// $device = new Device();
// $device->portReconnect($ip, $port, 'private');

// echo "<pre>";
// var_dump($device);
?>