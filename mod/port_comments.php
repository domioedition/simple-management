<?php
/*session_start();
include('../inc/conf.php');
include('../inc/functions.php');


$user_id = $_SESSION['userId'];
$date = time();
$ip_address = $_POST['ip_address'];
$port = $_POST['port'];
$comment = $_POST['comment'];





$sql = "SELECT * FROM logs_ports_comments where ip_address='$ip_address' AND port ='$port'";
$result = $link->query($sql);
$row = $result->fetch_assoc();

$id = $row['id'];

if($comment == ""){
	//Удаляем коментарий.
	$sql = "DELETE FROM logs_ports_comments WHERE id='$id'";
}else{
	if($result->num_rows > 0){
		$sql = "UPDATE logs_ports_comments SET user_id='$user_id', date='$date', ip_address='$ip_address', port='$port', comment='$comment' WHERE id='$id'";
	}else{
		$sql = "INSERT INTO logs_ports_comments (user_id, date, ip_address, port, comment) VALUES ('$user_id', '$date', '$ip_address', '$port', '$comment')";
	}
}
//Записываем событие в таблицу Логов.
// logSave($ip_address, $port, "comment");
echo $id;
if($link->query($sql) === TRUE){
	$link->close();
	header("Location: /index.php?ip=$ip_address");
	exit;
}else{
	echo "Error updating record: " . $link->error;
}


*/
?>