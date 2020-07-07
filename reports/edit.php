<?
session_start();
include '../inc/conf.php';
include '../inc/functions.php';
include 'reports.class.php';
/*Получаем данные из сессии*/
$userId = $_SESSION['userId'];
$login = $_SESSION['userLogin'];
$userAccess = $_SESSION['userAccess'];
checkAccess(14);

if($_SERVER["REQUEST_METHOD"] == "POST"){
	$id= $_POST['id'];
	$date = time();
	$userIdCreator = $_POST['userIdCreator'];
	$category = $_POST['category'];
	$content = $_POST['content'];
	if($userId == $userIdCreator){
		if(!empty($category)&& !empty($content)){
			$report = new Reports();
			$sql = "UPDATE reportsNew SET date='$date', user_id='$userId', category='$category', content='$content' WHERE id='$id'";
			// echo $sql;
			$report->updateRow($sql);
			header("Location: /reports/");
		}else{
			die("You did not fill in all fields!");
		}
	}else{
		die("This record was not created by you!");
	}
}

?>