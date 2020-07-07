<?php
//создать отдельного пользователя для базы данных
define ("DB_HOST","localhost");
define ("DB_LOGIN","root");
define ("DB_PASS","I18334oLWNc8");
define ("DB_NAME","sm");

$link = mysqli_connect(DB_HOST, DB_LOGIN, DB_PASS, DB_NAME) or die (mysqli_connect_error());
mysqli_query($link, "SET NAMES utf8");
mysqli_query($link, "SET CHARACTER SET 'utf8';"); 
mysqli_query($link, "SET SESSION collation_connection = 'utf8_general_ci';"); 	

$date= time();
$date = strftime("%d-%m-%y", $date);
//Запрос для опеределения последней записи в БД
$sql = "select date from reports order by id desc limit 1";
$result = mysqli_query($link, $sql);
$row = mysqli_fetch_assoc($result);
$d = strftime("%d-%m-%y", $row['date']);
//Если последння запись с датой равна сегодняшней дате, то вы водим сообщение, и запись не добаляем.
if($date == $d){
	echo "The record already exists in the database. \n";
}else{
	$date= time();
	$user_id = '1';
	$content1 = '';
	$content2 = '';
	$content3 = '';
	$content4 = '';

	//Запрос на добавление записи в бд.
	$sql = "INSERT INTO reports (date, user_id, content1, content2, content3, content4) VALUES (?, ?, ?, ?, ?, ?)";
	//echo $sql;
	if(!$stmt = mysqli_prepare($link, $sql)){
		die("Error.");
	}
	mysqli_stmt_bind_param($stmt,"iissss",$date, $user_id, $content1, $content2, $content3, $content4);
	if(mysqli_stmt_execute($stmt)){
		echo "New event added successfully.\n";
	}
	mysqli_stmt_close($stmt);
}
?>