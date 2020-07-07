<?php
//Проверка уровня доступа
if ($_SESSION['userAccess'] >= 14) {

if($_SERVER["REQUEST_METHOD"] == 'POST'){
	$name = $_POST['name'];
	$content = $_POST['content'];
}

if($_SERVER["REQUEST_METHOD"] == 'GET'){
	$action = $_GET['action'];
}


if($_GET['action'] != '' and $_GET['action'] != 'add' and $_GET['action'] != 'edit' and $_GET['action'] != 'delete'){
	echo '<h3>Unknown action.</h3>';
}
//Вывод всех заметок
if($_GET['action'] == '')
{
	echo '<p align="right"><a href="?page=notes&action=add"><button type="button" class="btn btn-primary">New</button></a></p>';
	$sql = "SELECT * FROM notes ORDER BY date DESC";
	$result = mysqli_query($link, $sql);
	while($row=mysqli_fetch_array($result))
	{
		$name = $row['name'];
		$content = nl2br($row['content']);
		$date = date("d-m-Y H:i", $row[date]);
		echo "
		<br>
		<br>
		<br>
		<h4>$name</h4>
		<small>$date</small>
		<hr>
		<p>$content</p>
		<p align=\"right\"><a href=\"?page=notes&action=edit&id=$row[id]\"><button type=\"button\" class=\"btn btn-default btn-sm\">E</button></a>
		<a href=\"?page=notes&action=delete&id=$row[id]\"><button type=\"button\" class=\"btn btn-default btn-sm\">D</button></a>
		</p>
		<br>
		<br>
		<br>
		";
	}
}



if($_GET['action'] == 'add')
{
	if($_POST['do'] == '')
	{
	//Выводим форму Добавления в БД
	?>
		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-default">
					<div class="panel-heading">Новая заметка</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-lg-8">
								<form role="form" action="" method="post">
								<div class="form-group">
									<input class="form-control" name="name" placeholder="Название">
								</div>
								<div class="form-group">
									<textarea class="form-control" rows="15" name="content" placeholder="Содержание"></textarea>
								</div>
									<input type="submit" value="Добавить" name="do" class="btn btn-primary">				
								</div>
								</form>
							</div>
					</div>
				</div>
			</div>
		</div>
	<?php		
	}
	else
	{
		// Тут мы добавляем запись в БД
		$date = time();
		$sql = "INSERT INTO notes (name, content, date) VALUES ('$name', '$content', '$date')";
		mysqli_query($link, $sql);
		//echo '<br>'.$name.'<br>'.$content.'<br>';		
		header("Location: index.php?page=notes");
	}
}

if($_GET['action'] == 'edit')
{
	if($_POST['do'] == '')
	{
		$id = $_GET['id'];
		$sql = "SELECT * FROM notes WHERE id='$id'";
		$result = mysqli_query($link, $sql);
		if(mysqli_num_rows($result)>0)
		{
			$row = mysqli_fetch_assoc($result);
			//print_r($row);
			$name = $row['name'];
			$content = $row['content'];
			$id = $row['id'];
			?>
			<div class="row">
				<div class="col-lg-12">
					<div class="panel panel-default">
						<div class="panel-heading">Изменить</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-lg-12">
									<form role="form" action="" method="post">
									<div class="form-group">
										<input class="form-control" name="name" placeholder="Название" value="<?=$name?>">
									</div>
									<div class="form-group">
										<textarea class="form-control" rows="15" name="content" placeholder="Содержание"><?=$content?></textarea>
									</div>
										<input name="id" value="<?=$id?>" hidden>
										<input type="submit" value="Сохранить" name="do" class="btn btn-primary">				
									</div>
									</form>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
		else
		{
			echo "<h4>Записи с идентификатором <code>$id</code> не найдено.</h4>";
		}
	}
	else
	{
		$id = $_POST['id'];
		$date = time();
		$sql ="UPDATE notes SET name='$name', content='$content', date='$date' WHERE id='$id'";
		if (mysqli_query($link, $sql))
		{
			echo "<h4>Record updated successfully.</h4>";
			echo '<p><a href="index.php?page=notes">Заметки</a>';
		}
		else
		{
			echo "Error updating record: " . mysqli_error($conn);
		}
		header("Location: index.php?page=notes");
	}
}


if($_GET['action'] == 'delete')
{
	$id = $_GET['id'];
	$sql = "DELETE FROM notes WHERE id='$id'";
	//print_r($sql);
	mysqli_query($link, $sql) or die("Error " .mysqli_error());
	echo '<h3>Запись успешно удалена.</h3>';
	echo '<a href="index.php?page=notes">Заметки</a>';			
	
}
//include('../inc/foot.inc.php');

} else {
	errorMsg("Извините у Вас нет прав на данное действие.");
}
?>