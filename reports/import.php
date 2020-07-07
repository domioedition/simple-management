<?
// session_start();
// include '../inc/conf.php';
// include '../inc/functions.php';
// include 'reports.class.php';

// $sql = "SELECT * FROM reports";
// $result = $link->query($sql);

// if ($result->num_rows > 0) {
// 	while($row = $result->fetch_assoc()) {
// 		$date = clearForDB($row['date']);
// 		$userId = clearForDB($row['user_id']);
// 		$content1 = clearForDB($row['content1']);
// 		$content2 = clearForDB($row['content2']);
// 		$content3 = clearForDB($row['content3']);
// 		$content4 = clearForDB($row['content4']);

// if($content1 != ''){
// 		$category = 1;
// 		$report = new Reports();
// 		$sql = "INSERT INTO reportsNew (date, user_id, category, content) VALUE('$date', '$userId', '$category', '$content1')";
// 		$report->insertRow($sql);
// 	}
// 	if($content2 != ''){
// 		$category = 2;
// 		$report = new Reports();
// 		$sql = "INSERT INTO reportsNew (date, user_id, category, content) VALUE('$date', '$userId', '$category', '$content2')";
// 		$report->insertRow($sql);
// 	}
// 	if($content3 != ''){
// 		$category = 3;
// 		$report = new Reports();
// 		$sql = "INSERT INTO reportsNew (date, user_id, category, content) VALUE('$date', '$userId', '$category', '$content3')";
// 		$report->insertRow($sql);
// 	}
// 	if($content4 != ''){
// 		$category = 4;
// 		$report = new Reports();
// 		$sql = "INSERT INTO reportsNew (date, user_id, category, content) VALUE('$date', '$userId', '$category', '$content4')";
// 		$report->insertRow($sql);
// 	}
//     }
// } else {
//     echo "0 results";
// }
// $link->close();
?>