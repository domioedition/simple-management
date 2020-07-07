<?
session_start();
include '../inc/conf.php';
include '../inc/functions.php';
// include 'reports.class.php';
// include '../inc/head.inc.php';
checkAccess(14);
if($_SERVER['REQUEST_METHOD'] == 'POST'){
	// print_r($_POST);
	$id = $_POST['id'];
	$date = time();
	$user_id = $_SESSION['userId'];
	$comment = $_POST['comment'];
	if(!empty($id) && !empty($comment)){
		$sql = "INSERT INTO reports_comment (report_id, date, user_id, comment) VALUES ('$id', '$date', '$user_id', '$comment')";
	
		if ($link->query($sql) === TRUE) {
			header("Location: /reports/?action=view&id=$id");
			exit();
		} else {
			echo "Error: " . $sql . "<br>" . $link->error;
		}
	} else {
		header("Location: /reports/?action=view&id=$id");
		exit();
	}	
}
?>