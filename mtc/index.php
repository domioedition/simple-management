<pre>
<?
error_reporting(0);
$tableName = $_POST['tableName'];
$field = $_POST['field'];
$type = $_POST['type'];
$length = $_POST['length'];
print_r($_POST);

// CREATE TABLE MyGuests (
// id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
// firstname VARCHAR(30) NOT NULL,
// lastname VARCHAR(30) NOT NULL,
// email VARCHAR(50),
// reg_date TIMESTAMP
// )

$sql = "CREATE TABLE $tableName ($field $type $length)";

$template = $sql;
?>
</pre>
<!DOCTYPE html>
<html>
<head>
	<title>Mysql Template Creator</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
<h1>Mysql Template Creator</h1>


<div id="wrapper">
	<div class="content">
	<div class="row">
	<form action="" method="post" id="formTemplate">
		<label>Table name</label><br>
		<input type="text" name="tableName"><br>
		<label>Field name</label><br>
		<input type="text" name="field1"><br>
		<label>Type</label><br>
		<select name="type1">
			<option id="integer">integer</option>
			<option id="varchar">varchar</option>
			<option id="text">text</option>
		</select><br>
		<label>Length</label><br>
		<input type="text" name="length1"><br>
		<button id="addColumn">Add column</button>
		<button id="go">Create</button>
		<!-- <input type="submit" name="" value="Create"> -->
	</form>
	</div>
		<div class="row">
			<textarea id="template" name="result" rows="20" cols="100"><?=$template;?></textarea>
		</div>
	<!-- //content -->
	</div>
</div>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script type="text/javascript" src="js/script.js"></script>
</body>
</html>