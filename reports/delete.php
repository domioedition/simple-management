<?
session_start();
include '../inc/conf.php';
include '../inc/functions.php';
include 'reports.class.php';

$userId = $_SESSION['userId'];
$login = $_SESSION['userLogin'];
$userAccess = $_SESSION['userAccess'];
checkAccess(14);

if($_SERVER["REQUEST_METHOD"] == "GET"){
	$id = $_GET['id'];
	if(!empty($id)){
		$report = new Reports();
		$report->deleteRow("DELETE FROM reportsNew WHERE id='$id'");
		$report->deleteRow("DELETE FROM reports_comment WHERE report_id='$id'");
		header("Location: /reports/");
	}else{
		exit("error_delete_page");
	}
}


?>