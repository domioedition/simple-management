<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);
$start_time = microtime(true);

/*Подключаем файлы с конфигурацией MySQL и функциями*/
require_once('inc/functions.php');
require_once('inc/conf.php');
require_once('inc/lib.inc.php');

/*Проверяем или авторизован пользователь*/
checkAuth();
$ip = $_GET['ip'];
/*Получаем данные из сессии*/
$userId = $_SESSION['userId'];
$login = $_SESSION['userLogin'];
$userAccess = $_SESSION['userAccess'];

//Переменная page. Нужна для подключения модулей и формирования заголовков.
$page = $_GET['page'];
//Формируем заголовки и название станиц.
if($page){
	switch($page){
		case 'nodes': $heading = "Nodes"; break;
		case 'manuals': $heading = "Manuals"; break;
		case 'reports': $heading = "Reports"; break;
		case 'rSupport': $heading = "Reports Support"; break;
		case 'user': $heading = "Users"; break;
		case 'checkoid': $heading = "Check OID"; break;
		case 'converter': $heading = "Converter Bytes to Mbits"; break;
		case 'searchPort': $heading = "Search port via mac-address"; break;
		case 'accessCreator': $heading = "Access Creator"; break;
		case 'TopologyAll': $heading = "Network Topology STP"; break;
		case 'lldp': $heading = "Network Topology LLDP"; break;
		case 'notes': $heading = "MyNotes"; break;
		case 'logs': $heading = "Logs"; break;
		case 'clients': $heading = "Clients"; break;
		case 'iptv': $heading = "IPTV"; break;
		case 'getModels': $heading = "Get models list"; break;
		default : $heading = "";
	}
}


include ('inc/head.inc.php');


/*Блок проверки авторизации пользователя - Начало*/
// если пользователь не авторизован
if (!isset($_SESSION['id']))
{
	// то проверяем его куки
	// вдруг там есть логин и пароль к нашему скрипту

	if (isset($_COOKIE['login']) && isset($_COOKIE['password']))
	{
		// если же такие имеются
		// то пробуем авторизовать пользователя по этим логину и паролю
		$login = mysql_escape_string($_COOKIE['login']);
		$password = mysql_escape_string($_COOKIE['password']);

		// и по аналогии с авторизацией через форму:

		// делаем запрос к БД
		// и ищем юзера с таким логином и паролем

		$query = "SELECT `id`
					FROM `users`
					WHERE `login`='{$login}' AND `password`='{$password}'
					LIMIT 1";
		$sql = mysqli_query($query) or die(mysqli_error());

		// если такой пользователь нашелся
		if (mysqli_num_rows($sql) == 1)
		{
			// то мы ставим об этом метку в сессии (допустим мы будем ставить ID пользователя)

			$row = mysqli_fetch_assoc($sql);
			$_SESSION['userId'] = $row['id'];

			// не забываем, что для работы с сессионными данными, у нас в каждом скрипте должно присутствовать session_start();
		}
	}
}



if (isset($_SESSION['userId']))
{
	$query = "SELECT `login`, `access`
				FROM `users`
				WHERE `id`='{$_SESSION['userId']}'
				LIMIT 1";
	$sql = mysqli_query($link, $query) or die(mysql_error());
	
	// если нету такой записи с пользователем
	// ну вдруг удалили его пока он лазил по сайту.. =)
	// то надо ему убить ID, установленный в сессии, чтобы он был гостем
	if (mysqli_num_rows($sql) != 1)
	{
		header('Location: login.php?logout');
		exit;
	}
	$row = mysqli_fetch_assoc($sql);
	$_SESSION['userLogin'] = $row['login'];
	$_SESSION['userAccess'] = $row['access'];
	unset($row);
}
else
{
	header('Location: login.php');
	exit;
}
/* Блок проверки авторизации пользователя - Конец */


/* Начало вывода основного контента */

//Вывод заголовков страниц.
// if(!isset($ip)){


// }

if($_SERVER['REQUEST_METHOD'] == 'GET')
{
	$ip = $_GET['ip'];
	$port = $_GET['port'];
	$action = $_GET['action'];
	$mod = $_GET['mod'];
	$p = $_GET['p'];
}

/*Блок подключаемых модулей*/

if(isset($page)){
	// echo '<div class="row"><div class="col-lg-12"><h1 class="page-header">'.$heading.'</h1></div></div>';
	echo "<h1>$heading</h1>";
	switch($page){
		case 'nodes': include 'mod/modNodes.php'; break;
		case 'reports': include 'mod/modReports.php'; break;
		case 'rSupport': include 'mod/modReportsSupport.php'; break;
		case 'user': include 'mod/modUser.php'; break;
		case 'user2': include 'mod/modUser2.php'; break;
		case 'checkoid': include 'mod/modOID.php'; break;
		case 'converter': include 'mod/modConverter.php'; break;
		case 'searchPort': include 'mod/modSearchPort.php'; break;
		case 'accessCreator': include 'mod/modAccessCreator.php'; break;
		case 'accessCreator2': include 'mod/modAccessCreator2.php'; break;
		case 'TopologyAll': include 'mod/modTopologyAll.php'; break;
		case 'notes': include 'mod/modNotes.php'; break;
		case 'logs': include 'mod/modLogs.php'; break;
		case 'getModels': include 'mod/modGetModels.php'; break;
		case 'clients': include 'mod/modClients.php'; break;
		case 'iptv': include 'mod/modIPTV.php'; break;
		case 'lldp': include 'mod/device/modLldp.php'; break;
		case 'test': include './test/index.php'; break;
		case 'manuals': include './manuals/index.php'; break;
		default : include 'mod/index.php';	
	}
}

/*Вывод страницы default.php*/
if(!isset($ip) && !isset($page)){
	include 'mod/index.php';
}


/*Блок управления свичем*/
/*проверяем свич на доступность.
Для этого отправляем snmp запрос чтобы узнать модель свича.
модель узнали - свич доступен*/
if(isset($ip) && !isset($page))
{
	//Запрос на получение модели свича
	getModel();
	
	
	//если модель отсутвует в масиве, то отсылаем на станицу с кодом ошибки №3
	if(!in_array($model, $modelArray)){
		header("Location: ../error.php?code=3");
	}
	
	//1 вариант
/* 	include('mod/modDevice.php');//подключаем блок с информацией о свиче
	//подключаем остальные модули
	switch($mod)
	{
		case 'port' : include 'mod/modPortView.php'; break;
		case 'save' : include 'mod/modSave.php'; break;
		case 'topology' : include 'mod/modTopology.php'; break;
		case 'checkOID' : include 'mod/modOID.php'; break;		
		default : include 'mod/deviceView.php';
	} */
	
	//2 вариант
	include('mod/device/index.php');
	
}

?>


<div class="row">
<div class="col-lg-12">
	<?
	//Вывод времени выполнения скрипта
	$end_time = microtime(true);
	echo '<h4>'.round(($end_time-$start_time),3).' сек</h4>';
	?>

</div>
</div>
<?
include('inc/foot.inc.php');
?>