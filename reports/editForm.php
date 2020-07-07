<?
include 'reports.class.php';
$id = $_GET['id'];
$report = new Reports();
$row = $report->getReport("SELECT * FROM reportsNew WHERE id='$id'");
$id = $row['id'];
$date = date('d-m-Y',$row['date']);
$user_id = $row['user_id'];
$category = $row['category'];
$content = $row['content'];
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="../../inc/my.css">
</head>
<body>
<form method="post" action="edit.php">
	<label>DATE: <?=$date?></label><br>
	<label>USER: <?=$user_id?></label><br>
	<label>Category</label><br>
	<select name="category">
		<option value="1">Network</option>
		<option value="2">Clients</option>
		<option value="3">Equipment</option>
		<option value="4">Other</option>
	</select><br>
	<label>Content</label><br>
	<textarea name="content"><?=$content?></textarea><br>
		<input type="text" name="id" value="<?=$id?>" hidden>
		<input type="text" name="user_id" value="<?=$user_id?>" hidden>
	<input type="submit" name="Change">
</form>
</body>
</html>