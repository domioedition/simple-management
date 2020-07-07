<?php
/*functions.php		-	В этом файле хранятся все функции к приложению*/

//Определяем список констант
define("rcomm","public");
define("wcomm","private");
define("timeout","0.3");
define("retries","1");

/*Проверка авторизации пользователя
 проеряем или авторизован пользователь.
 Если в сессии не установлен userId,
 то отправляем на страницу авторизации*/
function checkAuth(){
	if(!isset($_SESSION['userId'])){
		header('Location: ../../login.php');
		exit;
	}
}
/*Проверяем уровень доступа.
Если уровень доступа равен 1, то выводим информацию.
если нету, то редиректим на страницу Error.php
Такая функция во всех фалах.*/
function checkAccess($access=14){
	if(($_SESSION['userAccess']!=$access) or ($_SESSION['userAccess']<$access)){
		//print_r($_SESSION);
		
	}
	if($_SESSION['userAccess']>=$access){
		//echo $access;
	}else{
		header( "Location: ../../error.php?code=1");
		exit;
	}
}

//Функция Error
function Error($code=1){
	header( "Location: ../../error.php?code=$code");
	exit;
}

//Функция запроса на получение модели свича
function getModel(){
		global $ip, $model;
		$model = @snmpget($ip, rcomm, "1.3.6.1.2.1.1.1.0");
		if(is_string($model))
		{
			if(stripos($model, 'DES-3526') == true) $model='DES-3526';
			if(stripos($model, 'DES-3200') == true) $model='DES-3200';
			if(stripos($model, 'DES-3528') == true) $model='DES-3528';
		}
		else
		{
			header('Location: ../../error.php?code=2&ip='.$ip);
			exit();
		}
}


//функция подсветки кода
function highlight($search, $str){
	$pattern = "/((?:^|>)[^<]*)(".$search.")/ius"; //регулярное выражение
	$replace = '$1<b style="font-size:16px; color:#333; background:#64ee9e;">$2</b>'; // шаблон замены строки
	$str = preg_replace($pattern, $replace, $str); // замена
	return $str;
}



function clearErrors($ip, $port){
	global $model;
	switch($model){
		case 'DES-3526': $res = @snmpset($ip,wcomm,"1.3.6.1.4.1.171.11.64.1.2.1.2.8.0", "i", 2); break;
		case 'DES-3200': $res = @snmpset($ip, wcomm, "1.3.6.1.4.1.171.11.113.1.5.2.1.2.12.0", "i", 2); break;
		case 'DES-3528': $res = @snmpset($ip, wcomm, "1.3.6.1.4.1.171.11.105.1.2.1.2.6.0", "i", 2); break;
	}
	if($res)
	{
		successMsg("All errors destroyed. ");
	}else{
		errorMsg("Ошибки не были очищены. $model. ");
	}
}


//Функиця вывода сообщения
function Msg($msg,$h=1){
	echo "<center><h$h>$msg</h$h></center>";
}


//Функция вывода сообщения об ошибке
function errorMsg($msg){
echo '
<h3 class="page-header">Error</h3>
<div class="alert alert-danger">'.$msg.' <a class="alert-link" href="javascript:history.back()"> Сome back.</a></div>
';
}
//Функция вывода сообщения об успешном действии
function successMsg($msg){
	echo '<h3 class="page-header">Success</h3><div class="alert alert-success alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><h4>'.$msg.'</h4></div>';
}
//Функция очистки от html тегов и пробелов при добавлении в БД
function clearForDB($data){
	global $link;
	return trim(strip_tags($data));
}

// Function to get the client IP address
function get_client_ip() {
    $ipaddress = '';
    if ($_SERVER['HTTP_CLIENT_IP'])
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if($_SERVER['HTTP_X_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if($_SERVER['HTTP_X_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if($_SERVER['HTTP_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if($_SERVER['HTTP_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if($_SERVER['REMOTE_ADDR'])
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}


//Функия которая очищает строку от текста 'STRING: '
function clearSTRING($str) {
	$str = str_replace("STRING: \"","", $str);
	$str = substr($str,0, strlen($str)-1);
	return $str;
}
//Функия которая очищает строку от текста 'INTEGER: '
function clearINTEGER($str) {
	$str = str_replace("INTEGER: ","", $str);
	return $str;
}
//Функия которая очищает строку от текста 'IpAddress: '
function clearIpAddress($str) {
	$str = str_replace("IpAddress: ","", $str);
	return $str;
}
//Очиащаем строку от "Counter32:"
function strRem($data){
	return str_replace("Counter32:","","$data");
}

/*
** Функция для генерации соли, используемоей в хешировании пароля
** возращает 3 случайных символа
*/
function GenerateSalt($n=3)
{
	$key = '';
	$pattern = '1234567890abcdefghijklmnopqrstuvwxyz.,*_-=+';
	$counter = strlen($pattern)-1;
	for($i=0; $i<$n; $i++)
	{
		$key .= $pattern{rand(0,$counter)};
	}
	return $key;
}





// $t массив с кольцами и картами.
$t = array();
$t['Map1']['Ring1'] = 'Ring1';
$t['Map1']['Ring3'] = 'Ring3';
$t['Map1']['Ring4'] = 'Ring4';
$t['Map1']['Ring5'] = 'Ring5';
$t['Map1']['Ring6'] = 'Ring6';
$t['Map1']['Ring9'] = 'Ring9';
$t['Map1']['Ring10'] = 'Ring10';
$t['Map1']['Ring11'] = 'Ring11';
$t['Map1']['Ring12'] = 'Ring12';
$t['Map2']['Ring1'] = 'Ring1';
$t['Map2']['Ring2'] = 'Ring2';
$t['Map3']['Ring1'] = 'Ring1';
$t['Map3']['Ring2'] = 'Ring2';
$t['Map3']['Ring3'] = 'Ring3';
$t['Map3']['Ring4'] = 'Ring4';
$t['Map3']['Ring5'] = 'Ring5';
$t['Map3']['Ring6'] = 'Ring6';
$t['Map5']['Ring1'] = 'Ring1';
$t['Map5']['Ring2'] = 'Ring2';
$t['Map5']['Ring5'] = 'Ring5';
$t['Map6']['Ring1'] = 'Ring1';
$t['Map6']['Ring2'] = 'Ring2';
$t['Map7']['Ring1'] = 'Ring1';
$t['Map8']['Ring1'] = 'Ring1';
$t['Map8']['Ring2'] = 'Ring2';
$t['Map9']['Ring1'] = 'Ring1';
$t['Map9']['Ring3'] = 'Ring3';
$t['Map9']['Ring4'] = 'Ring4';
$t['Map9']['Ring5'] = 'Ring5';
$t['Map9']['Ring6'] = 'Ring6';
$t['Map10']['Ring1'] = 'Ring1';
$t['Map10']['Ring2'] = 'Ring2';
$t['Map12']['Ring1'] = 'Ring1';
$t['Map12']['Ring2'] = 'Ring2';
$t['Map12']['Ring3'] = 'Ring3';
$t['Map12']['Ring4'] = 'Ring4';
$t['Map13']['Ring1'] = 'Ring1';
$t['Map13']['Ring2'] = 'Ring2';
$t['Map15']['Ring1'] = 'Ring1';


// $cityMapRing массив с названием городов, картами и кольцами.
$cityMapRing = array();
$cityMapRing['LV']['Map1']['Ring1'] = 'Ring1';
$cityMapRing['LV']['Map1']['Ring3'] = 'Ring3';
$cityMapRing['LV']['Map1']['Ring4'] = 'Ring4';
$cityMapRing['LV']['Map1']['Ring5'] = 'Ring5';
$cityMapRing['LV']['Map1']['Ring6'] = 'Ring6';
$cityMapRing['LV']['Map1']['Ring9'] = 'Ring9';
$cityMapRing['LV']['Map1']['Ring10'] = 'Ring10';
$cityMapRing['LV']['Map1']['Ring11'] = 'Ring11';
$cityMapRing['LV']['Map1']['Ring12'] = 'Ring12';
$cityMapRing['LV']['Map2']['Ring1'] = 'Ring1';
$cityMapRing['LV']['Map2']['Ring2'] = 'Ring2';
$cityMapRing['LV']['Map3']['Ring1'] = 'Ring1';
$cityMapRing['LV']['Map3']['Ring2'] = 'Ring2';
$cityMapRing['LV']['Map3']['Ring3'] = 'Ring3';
$cityMapRing['LV']['Map3']['Ring4'] = 'Ring4';
$cityMapRing['LV']['Map3']['Ring5'] = 'Ring5';
$cityMapRing['LV']['Map3']['Ring6'] = 'Ring6';
$cityMapRing['LV']['Map5']['Ring1'] = 'Ring1';
$cityMapRing['LV']['Map5']['Ring2'] = 'Ring2';
$cityMapRing['LV']['Map5']['Ring5'] = 'Ring5';
$cityMapRing['LV']['Map6']['Ring1'] = 'Ring1';
$cityMapRing['LV']['Map6']['Ring2'] = 'Ring2';
$cityMapRing['LV']['Map7']['Ring1'] = 'Ring1';
$cityMapRing['LV']['Map8']['Ring1'] = 'Ring1';
$cityMapRing['LV']['Map8']['Ring2'] = 'Ring2';
$cityMapRing['LV']['Map9']['Ring1'] = 'Ring1';
$cityMapRing['LV']['Map9']['Ring3'] = 'Ring3';
$cityMapRing['LV']['Map9']['Ring4'] = 'Ring4';
$cityMapRing['LV']['Map9']['Ring5'] = 'Ring5';
$cityMapRing['LV']['Map9']['Ring6'] = 'Ring6';
$cityMapRing['LV']['Map10']['Ring1'] = 'Ring1';
$cityMapRing['LV']['Map10']['Ring2'] = 'Ring2';
$cityMapRing['LV']['Map12']['Ring1'] = 'Ring1';
$cityMapRing['LV']['Map12']['Ring2'] = 'Ring2';
$cityMapRing['LV']['Map12']['Ring3'] = 'Ring3';
$cityMapRing['LV']['Map12']['Ring4'] = 'Ring4';
$cityMapRing['LV']['Map13']['Ring1'] = 'Ring1';
$cityMapRing['LV']['Map13']['Ring2'] = 'Ring2';
$cityMapRing['LV']['Map15']['Ring1'] = 'Ring1';




//Массив с моделями
$modelArray = array();
$modelArray[] = 'DES-3526';
$modelArray[] = 'DES-3528';
$modelArray[] = 'DES-3200';



//Функиця проверки порта. Смотрим или порт входит в массив из 1-24, если нет то редиректим на страницу с ошибкой.
function checkPort($port){
	//Создаем массив из 24 портов. И проверяем или наш порт есть в массиве.
	$portsArr = array();
	for($i=1;$i<=24;$i++){
		$portsArr[] = $i;
	}
	if (!in_array($port, $portsArr)) {
		header("Location: ../../error.php?code=4");
//		die("Actions with the port are not allowed. ");		
	}
}


###############Запись логов в БД########################
//function logSave
function logSave($ip, $port, $action){
	global $link;
	$user_id = $_SESSION['userId'];
	$date = time();	
	$sql = "INSERT INTO logs_ports_action (user_id, date, ip_address, port, action) VALUES ('$user_id', '$date', '$ip', '$port', '$action')";
	if(!$link->query($sql)){
		echo "Error: " . $sql . "<br>" . $conn->error;
	}
	$link->close();
}
#################################################################

###функция записи в БД о некорректных попытках авторизации.
function loginError($login, $ip_address){
	global $link;
	$date = time();
	$sql = "INSERT INTO log_login_error (login, date, ip_address) VALUES ('$login', '$date', '$ip_address')";
	if(!$link->query($sql)){
		echo "Error: " . $sql . "<br>" . $conn->error;
	}
	$link->close();
}

//Проверяем или включен RMON
function checkRmon($ipAddress, $rcomm="public", $model){
	switch ($model) {
		case 'DES-3526':
			$request = snmpwalk($ipAddress, $rcomm, "1.3.6.1.4.1.171.11.64.1.2.1.2.3.0");		
			break;
		case 'DES-3200':
			$request = snmpwalk($ipAddress, $rcomm, "1.3.6.1.4.1.171.11.113.1.5.2.1.2.16.0");		
			break;
/*		case 'DES-3528':
			$request = snmpwalk($ipAddress, $rcomm, "1.3.6.1.4.1.171.11.113.1.5.2.1.2.16.0");		
			break;*/
		
		default:
			# code...
			break;
	}
	if($model != "DES-3528"){
		$request = $request[0]{strlen($request[0])-1};
		//Если rmon выключен, то отображаем сообщение.
		if($request[0] == 2){
			echo "<h2>Для отображения необходимо включить Rmon.</h2>";
		}
	}
}

/*
RMON
DES-3526 		1.3.6.1.4.1.171.11.64.1.2.1.2.3.0		если ответит INTEGER: 3 включён
snmpset ... 1.3.6.1.4.1.171.11.64.1.2.1.2.3.0 i 3		включает	i 2 - выключен
3200			1.3.6.1.4.1.171.11.113.1.5.2.1.2.16.0
*/

//Функция отключения port_security на DES-3200
function port_security($ip, $port, $model){
	checkPort($port);
	//Запись логов в БД
	$log = "$model - port_security";
	logSave($ip, $port, $log);
	if ($model != "DES-3200") {
		errorMsg("Only DES-3200. Your model is $model.");
		return NULL;
	}
	if($model == "DES-3200"){
		$port_security_status = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.11.113.1.5.2.15.1.1.4.$port");
		if ($port_security_status[0] == "INTEGER: 2") {
			//config port_security ports $port admin_state disable
			snmpset($ip, wcomm, "1.3.6.1.4.1.171.11.113.1.5.2.15.1.1.4.$port", "i", 3);
			//disable port
			snmpset($ip,wcomm,"1.3.6.1.4.1.171.11.113.1.5.2.2.2.1.3.".$port.".100",'i',2);
			//enable port
			snmpset($ip,wcomm,"1.3.6.1.4.1.171.11.113.1.5.2.2.2.1.3.".$port.".100",'i',3);
			//config port_security ports $port admin_state enable
			snmpset($ip, wcomm, "1.3.6.1.4.1.171.11.113.1.5.2.15.1.1.4.$port", "i", 2);
			Msg("Port security переподкюченно. Нажмите кнопку посмотреть ShowMac.", 3);
		} else {
			Msg("Port security уже отключена давно.", 3);
		}
	}
}

//Функиция проверяет есть ли 10 аксес на порту
function get_access_ip($ip, $port, $model){
	checkPort($port);
	switch ($model) {
		case 'DES-3526':
			$access_ip = @snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.12.9.2.2.1.4.10.21$port");
			break;
		case 'DES-3200':
			$access_ip = @snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.12.9.3.2.1.4.10.21$port");
			break;
		case 'DES-3528':
			$access_ip = @snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.12.9.3.2.1.4.10.$port");
			break;
		default: $access_ip = "Unknown model";
		break;
	}
	//Если получили true ответ от свича, тогда вытягиваем IP адрес из строки, ни или указываем что ip адрес отсутствует.
	if($access_ip){
		$access_ip = str_replace("IpAddress: ", "", $access_ip[0]);
		$class = "success";
	} else {
		$access_ip = "Missing 10 access."; //missing 10 access
		$class = "danger";
	}
	$result = "<div class=\"panel-body\"><div class=\"alert alert-$class alert-dismissable\">
				<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>
				10 access: <code>$access_ip</code>
				</div></div>";
	return $result;
}
?>