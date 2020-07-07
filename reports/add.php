<?
session_start();
include 'reports.class.php';

if($_SERVER["REQUEST_METHOD"] == "POST"){
	$report = new Reports();
	$date = time();
	$userId = $_SESSION['userId'];
	$category = $_POST['category'];
	$content = $_POST['content'];

	if(!empty($category) && !empty($content)){
		$report = new Reports();
		$report->insertRow("INSERT INTO reportsNew (date, user_id, category, content) VALUE('$date', '$userId', '$category', '$content')");
		header("Location: /reports/");
	}else{
		echo "You did not fill in all fields!";
	}
}
?>