<?
session_start();
// die("Временно не доступно.");
include '../inc/conf.php';
include '../inc/functions.php';
include 'reports.class.php';

/*Получаем данные из сессии*/
$userId = $_SESSION['userId'];
$login = $_SESSION['userLogin'];
$userAccess = $_SESSION['userAccess'];
checkAccess(14);

if(isset($_GET['ip'])){
	$ip = $_GET['ip'];
}else{
	$ip = '';
}

include '../inc/head.inc.php';

if(isset($_GET['action'])){
	$action = $_GET['action'];
}else{
	$action = '';
}




echo '<a href="../reports/">Reports</a>';
echo '<div id="row">
	<div class="col-lg-12">
		<h1 class="page-header">Reports</h1>
	</div>
</div>';
switch ($action) {
	case 'add':
		include 'addForm.php';
		break;
	case 'view':
		include 'view.php';
		break;
	// case 'delete':
	// 	include 'delete.php';
	// 	break;
	// case 'addComment':
	// 	include 'addComment.php';
	// 	break;
	
	default:
		include 'default.php';
		break;
}

?>



<?
include '../inc/foot.inc.php';
?>