<?
/*
	Модуль отчета тех поддержки о событиях происходящих за смену.
*/
if($_SERVER["REQUEST_METHOD"] == 'POST'){
	$search = $_POST['search'];
}



//Вывод всех событий
if($_GET['action'] == "" && $search==''){
	$reportsArr = array();
	$sql = " SELECT reports.date, reports.content1, users.login FROM reports INNER JOIN users ON users.id=reports.user_id ORDER BY reports.date DESC limit 15";
	$result = $link->query($sql);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			$id = $row['id'];
			$date = date("d-m-Y",$row['date']);
			$time = date("H:i:s",$row['date']);
			$login = $row['login'];
			$content1 = $row['content1'];
			$reportsArr[$date][] = $id."|".$date."|".$time."|".$login."|".$content1;
		}
	} else {
		echo "0 results";
	}
	$sql = "SELECT reports_support.id,reports_support.date,reports_support.user_id,reports_support.content1, users.login FROM reports_support INNER JOIN users ON users.id=reports_support.user_id ORDER BY reports_support.date DESC";
	$result = $link->query($sql);
	if ($result->num_rows > 0) {
		// output data of each row
		while($row = $result->fetch_assoc()) {
			$id = $row['id'];
			$date = date("d-m-Y",$row['date']);
			$time = date("H:i:s",$row['date']);
			$login = $row['login'];
			$content1 = $row['content1'];
			$reportsArr[$date][] = $id."|".$date."|".$time."|".$login."|".$content1;
		}
	} else {		
		// Msg("0 results in REPORTS_SUPPORT",3);
	}
echo <<<LINE
<div class="row">
<div class="col-md-4">
<a href="index.php?page=rSupport&action=add"><button type="button" class="btn btn-success">Add report</button></a>
</div>
<div class="col-md-8">
<form role="form" action="" method="post">
<div class="form-group input-group">
<input type="text" name="search" class="form-control" placeholder="Search">
<span class="input-group-btn">
<button class="btn btn-default" type="submit" title="Click to search"><i class="fa fa-search"></i>
</button>
</span>
</div>
</form>
</div>
LINE;
	foreach ($reportsArr as $key => $value) {
		echo "<h4>$key</h4>";
		foreach ($value as $k => $v) {
			$reccord = explode("|", $v);
			$id = $reccord[0];
			$date = $reccord[1];
			$time = $reccord[2];
			$login = $reccord[3];
			$content1 = nl2br($reccord[4]);
			if($content1 == "") $content1 = "Записи за этот день отсутствуют.";
if($login == "admin"){
echo <<<LINE
<div class="col-lg-12">
<div class="panel panel-default">
<div class="panel-heading">$time - $login</div>
<div class="panel-body">
<p>$content1</p>
</div>
</div>
</div>
LINE;
}else{
	echo <<<LINE
<div class="col-lg-12">
<div class="panel panel-info">
<div class="panel-heading">$time - $login</div>
<div class="panel-body">
<p>$content1</p>
</div>
<div class="panel-footer">
	<p align="right"><a href="?page=rSupport&action=edit&id=$id"><button type="button" class="btn btn-default btn-sm">E</button></a>
	<a href="?page=rSupport&action=delete&id=$id"><button type="button" class="btn btn-default btn-sm" onclick="return confirm('Вы действительно хотите удалить запись?') ">D</button></a>
	</p>
</div>
</div>
</div>
LINE;
}
		}
	}
}

if($_GET['action'] == 'add'){
	if ($_POST['do'] == '') {
	?>
		<div class="panel panel-default">
		<div class="panel-heading">Create report</div>
		<div class="panel-body">
		<div class="row">
		<div class="col-lg-12">
		<form role="form" action="" method="post">
			<div class="form-group">
				<textarea class="form-control" rows="7" name="content1" placeholder="Some text"></textarea>
			</div>
		<input type="submit" value="Create" name="do" class="btn btn-primary">				
		</form>
		
		</div>
		</div>
		</div>
		</div>

	<?
	} else {
		// Тут мы добавляем запись в БД
		$date = time();
		$userId = $_SESSION['userId'];
		$content1 = $_POST['content1'];
		if($content1 != ""){
			$sql = "INSERT INTO reports_support (date, user_id, content1) VALUES ('$date', '$userId', '$content1')";
			if ($link->query($sql) === TRUE) {
				header("Location: index.php?page=rSupport");
			} else {
				echo "Error: " . $sql . "<br>" . $link->error;
			}
		}else{
			errorMsg("Заполните все поля.");
		}
	}
}


if ($_GET['action'] == 'delete') {
	$id = $_GET['id'];
	if($id == ""){
		errorMsg("Unknown Id.");
	}else{
		$sql ="SELECT user_id FROM reports_support WHERE id='$id'";
		$result = $link->query($sql);
		$row = $result->fetch_row();
		$result->close();
		$cuurentID = $_SESSION['userId'];
	//проверяем если пользователь с правами админа, то ему все можно.
		if($_SESSION['userAccess'] >= 14){
			$sql = "DELETE FROM reports_support WHERE id='$id'";
			if ($link->query($sql) === TRUE) {
				header("Location: index.php?page=rSupport");
			} else {
				echo "Error: " . $sql . "<br>" . $link->error;
			}
		}
	//проверяем или запись изменяет тот юзер который создал и сейчас активен.
		elseif ($row[0] == (int)$cuurentID) {
			$sql = "DELETE FROM reports_support WHERE id='$id'";
			if ($link->query($sql) === TRUE) {
				header("Location: index.php?page=rSupport");
			} else {
				echo "Error: " . $sql . "<br>" . $link->error;
			}
		} else {
			errorMsg("У вас нет прав.");
		}
	}
}

if($_GET['action'] == 'edit')
{
	if($_POST['do'] == '')
	{
		$id = $_GET['id'];
		$sql = "SELECT * FROM reports_support WHERE id='$id'";
		$result = mysqli_query($link, $sql);
		if(mysqli_num_rows($result)>0)
		{
		$row = mysqli_fetch_assoc($result);
		$content1 = $row['content1'];
		$id = $row['id'];
		?>
		<div class="panel panel-default">
		<div class="panel-heading">Edit report</div>
		<div class="panel-body">
		<div class="row">
		<div class="col-lg-12">
		<form role="form" action="" method="post">
			<div class="form-group">
				<textarea class="form-control" rows="20" name="content1" placeholder="Some text"><?=$content1?></textarea>
			</div>
		<input name="id" value="<?=$id?>" hidden>
		<input type="submit" value="Change" name="do" class="btn btn-primary">				
		</form>
		
		</div>
		</div>
		</div>
		</div>			
		<?
		}
		else {
			errorMsg("Записи с идентификатором <code>$id</code> не найдено.");
		}
	}
	//Если передана форма POST , берем ID записи и обновляем в таблице, предварительно проверив.
	else {
		$id = $_POST['id'];
		if ($id == "") {
			errorMsg("Записи с идентификатором <code>$id</code> не найдено.");
		} else {
			$sql ="SELECT user_id FROM reports_support WHERE id='$id'";
			$result = $link->query($sql);
			if ($result->num_rows > 0) {
				$row = $result->fetch_row();
				$result->close();
				$cuurentID = $_SESSION['userId'];
				$date = time();
				$content1 = $_POST['content1'];
				//проверяем если пользователь с правами админа, то ему все можно.
				if($_SESSION['userAccess'] >= 14){
					$sql ="UPDATE reports_support SET date='$date', content1='$content1' WHERE id='$id'";
					if ($link->query($sql) === TRUE) {
						header("Location: index.php?page=rSupport");
					} else {
						echo "Error: " . $sql . "<br>" . $link->error;
					}
				}
				//проверяем или запись изменяет тот юзер который создал и сейчас активен.
				elseif ($row[0] != (int)$cuurentID) {
					errorMsg("У вас нет прав.");
				} else {
					$sql ="UPDATE reports_support SET date='$date', content1='$content1' WHERE id='$id'";
					if ($link->query($sql) === TRUE) {
						header("Location: index.php?page=rSupport");
					} else {
						echo "Error: " . $sql . "<br>" . $link->error;
					}
				}
			}
		}
	}
}


//Блок поиска
if(isset($search) && ($search!='')){
?>
<div class="row">
<div class="col-md-4">
<a href="index.php?page=rSupport&action=add"><button type="button" class="btn btn-success">Add report</button></a>
</div>
<div class="col-md-8">
<form role="form" action="" method="post">
<div class="form-group input-group">
<input type="text" name="search" class="form-control" placeholder="Search">
<span class="input-group-btn">
<button class="btn btn-default" type="submit" title="Click to search"><i class="fa fa-search"></i>
</button>
</span>
</div>
</form>
</div>
	<?php
	$sql = "SELECT reports.date, reports.content1 FROM reports WHERE content1 LIKE '%$search%' ORDER BY date DESC";
	$result = mysqli_query($link, $sql);
	$row_cnt = $result->num_rows;
echo <<<line
<div class="row">
<div class="col-lg-4">
	<h4>Search result: <code>$row_cnt</code></h4>
</div>
</div>
line;
if(mysqli_num_rows($result)>0){
	while($row = $result->fetch_assoc()){
		$id = $row['id'];
		$date = date("d-m-Y",$row['date']);
		$content1 = highlight($search, nl2br($row['content1']));
		// $content2 = highlight($search, nl2br($row['content2']));
		// $content3 = highlight($search, nl2br($row['content3']));
		// $content4 = highlight($search, nl2br($row['content4']));
	?>
	<div class="row">
		<div class="col-lg-12">
			<h3 class="page-header"><?=$date?></h3>
		</div>
		<div class="col-lg-12">
				<div class="row">
					<div class="col-lg-12">
						<div class="well well-sm">
							<h4>Network</h4>
							<p><?=$content1;?></p>
						</div>
					</div>
				</div>
		</div>
	</div>
	<br>
	<br>
	<br>
	<br>
	<?
}	
}
}
?>