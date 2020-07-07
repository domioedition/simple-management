<?php
checkAccess(14);
if($_SERVER["REQUEST_METHOD"] == 'POST'){
	$search = $_POST['search'];
	$selectedDate = $_POST['firstinput'];	
}

$action = $_GET['action'];

/*###################	Блок постраничная навигация ##################*/
//значение текущей страницы из GET
$p = intval($_GET['p']);
//Переменная хранит число сообщений выводимых на станице
$num = 7;
if ($p==0) $p=1;
//Определяем общее число сообщений в базе данных
$query = "SELECT count(`id`) FROM `reports`";
$mysql_result = mysqli_query($link, $query);
if(mysqli_num_rows($mysql_result)>0){
	$count=mysqli_fetch_row($mysql_result);
}else{
	die("0 records");
}
$posts = $count[0]; // получем значение кол-во всех записей
// Находим общее число страниц
$total = intval(($posts - 1) / $num) + 1;
// Определяем начало сообщений для текущей страницы
$p = intval($p);
// Если значение $p меньше единицы или отрицательно
// переходим на первую страницу
// А если слишком большое, то переходим на последнюю
if(empty($p) or $p < 0) $p = 1;
if($p > $total) $p = $total;
// Вычисляем начиная к какого номера
// следует выводить сообщения
$start = $p * $num - $num;
// Проверяем нужны ли стрелки назад
if ($p != 1) $pervpage = '<a href="?page=reports&p=1"><<</a><a href="?page=reports&p='. ($p - 1).'"><</a> ';
// Проверяем нужны ли стрелки вперед
if ($p != $total) $nextpage = '  <a href="?page=reports&p='. ($p + 1).'">></a>
<a href="?page=reports&p='.$total.'">>></a> ';
// Находим две ближайшие станицы с обоих краев, если они есть
if($p - 2 > 0) $p2left = ' <a href="?page=reports&p='. ($p - 2) .'">'. ($p - 2) .'</a>  ';
if($p - 1 > 0) $p1left = '<a href="?page=reports&p='. ($p - 1) .'">'. ($p - 1) .'</a>  ';
if($p + 2 <= $total) $p2right = '  <a href="?page=reports&p='. ($p + 2).'">'. ($p + 2) .'</a>';
if($p + 1 <= $total) $p1right = '  <a href="?page=reports&p='. ($p + 1).'">'. ($p + 1) .'</a>';
/*###################
						Конец блока постраничной навигации
###################*/
 
//Блок удаления
if($action == 'delete')
{
	checkAccess(15);
	$id = $_GET['id'];
	$sql = "SELECT `id` FROM reports WHERE id='$id'";
	$result = mysqli_query($link, $sql) or die("Error " .mysqli_error());
	if(mysqli_num_rows($result)>0){
		$sql = "DELETE FROM reports WHERE id='$id'";
		$result = mysqli_query($link, $sql) or die("Error " .mysqli_error());
		successMsg("Запись успешно удалена. <a href=\"index.php?page=reports\">Reports</a>");
	}else{
		errorMsg("Запись не найдена!");
	}
}

//Блок редактирование события
if($action == 'edit'){
	checkAccess(14);
	if($_POST['do'] == '')
	{
		$id = $_GET['id'];
		$sql = "SELECT * FROM reports WHERE id='$id'";
		$result = mysqli_query($link, $sql);
		if(mysqli_num_rows($result)>0)
		{
			$row = mysqli_fetch_assoc($result);
			$date = date("d-m-Y",$row['date']);
			$content1 = $row['content1'];
			$content2 = $row['content2'];
			$content3 = $row['content3'];
			$content4 = $row['content4'];
			?>
			<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-default">
				<div class="panel-heading">Date: <?=$date?></div>
				<div class="panel-body">
			
				<form role="form" action="" method="post">
				
				<div class="row">
				<div class="col-lg-6">
					<div class="form-group">
						<label>Network</label>
						<textarea class="form-control" rows="7" name="content1" placeholder="Зміни в топології, Забанені\Розбанені порти, Флуд, т.д."><?=$content1?></textarea>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="form-group">
						<label>Clients</label>
						<textarea class="form-control" rows="7" name="content2" placeholder="Бізнес клієнти, дзвінки, скарги, проблеми і рішення"><?=$content2?></textarea>
					</div>
				</div>
				</div>
				<div class="row">
				<div class="col-lg-6">
					<div class="form-group">
						<label>Change equipment</label>
						<textarea class="form-control" rows="7" name="content3" placeholder="Заміна свічів"><?=$content3?></textarea>
					</div>
				</div>
				<div class="col-lg-6">
				<div class="form-group">
						<label>Other</label>
						<textarea class="form-control" rows="7" name="content4" placeholder="Інші події за день(хто для кого шо залишав, прийшов Адмінчик переткнув шось і т.д.)"><?=$content4?></textarea>
					</div>
				</div>
				</div>
					<input name="id" value="<?=$id?>" hidden>
					<input name="p" value="<?=$p?>" hidden>
					<input type="submit" value="Save" name="do" class="btn btn-primary">
				</form>

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
		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			$id = $_POST['id'];
			$date = time();
			$user_id = $_SESSION[userId];
			$content1 = clearForDB($_POST['content1']);
			$content2 = clearForDB($_POST['content2']);
			$content3 = clearForDB($_POST['content3']);
			$content4 = clearForDB($_POST['content4']);
			//Переменная $р нужна для того, чтобы после изменений в БД редиректить на ту же самую страницу.
			$p = $_POST['p'];
			$sql = "UPDATE reports SET content1='$content1',content2='$content2',content3='$content3',content4='$content4' WHERE id='$id'";
			mysqli_query($link, $sql);
			header("Location: ../index.php?page=reports&p=$p");
			exit();
			// echo '<h4>Please press on link - <a href="../index.php?page=reports">Reports</a></h4>';
		}
	}
}

//Вывод всех событий
if($action == '' && $search=='' && $selectedDate == ''){
checkAccess(14);
?>
<div class="row">
	<div class="col-md-6">
		<form role="form" action="" method="post">
			<div class="form-group input-group">
				<input type="text" name="search" value="<?=$search?>" class="form-control" placeholder="Search">
				<span class="input-group-btn">
					<button class="btn btn-default" type="submit" title="Click to search"><i class="fa fa-search"></i>
					</button>
				</span>
			</div>
		</form>
	</div>
	<div class="col-md-2">
		<!--<a href="?page=reports&action=add" title="Add new record"><button type="button" class="btn btn-success">Add new</button></a>-->
	</div>
	<!-- Поиск по конкретной дате
	<div class="col-md-4">
		<form name="sampleform" action="" method="post">
		<input hidden type="text" name="firstinput" size=20><a href="javascript:showCal('Calendar1')"><button type="button" class="btn btn-default">Select Date</button></a>
		<button type="submit" class="btn btn-default">Show Date</button>
		</form>
	</div>
	-->
</div>
<div class="row">
	<div class="col-md-6">
	<ul class="pagination pagination-sm">
	<?php
		//Вывод постраничной навигации если страниц больше чем одна		
		if ($total>1) echo '<li>'.@$pervpage.'</li><li>'.@$p2left.'</li><li>'.@$p1left.'</li><li class="active"><span>'.@$p.'</span></li><li>'.@$p1right.'</li><li>'.@$p2right.'</li><li>'.@$nextpage.'</li>';
	?>
	</ul>	
	</div>
	<div class="col-md-3"></div>
	<div class="col-md-3"></div>
</div>
<?php	
$sql = "SELECT * FROM reports ORDER BY date DESC LIMIT $start, $num";
$result = mysqli_query($link, $sql);
while($row = $result->fetch_assoc()){
	$id = $row['id'];
	$date = date("d-m-Y",$row['date']);
	$content1 = nl2br($row['content1']);
	$content2 = nl2br($row['content2']);
	$content3 = nl2br($row['content3']);
	$content4 = nl2br($row['content4']);
?>
<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header"><?=$date?></h3>
	</div>
	<div class="col-lg-12">
			<div class="row">
				<div class="col-lg-6">
					<div class="well well-sm">
						<h4>Network</h4>
						<p><?=$content1;?></p>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="well well-sm">
						<h4>Clients</h4>
						<p><?=$content2;?></p>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-6">
					<div class="well well-sm">
						<h4>Change equipment</h4>
						<p><?=$content3;?></p>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="well well-sm">
						<h4>Other</h4>
						<p><?=$content4;?></p>
					</div>
				</div>
			</div>
			<a href="../index.php?page=reports&action=edit&id=<?=$id?>"><button class="btn btn-primary btn-xs">Edit</button></a>
			<?php
				if($_SESSION['userAccess'] == '15')	echo "<a href=\"?page=reports&action=delete&id=$id\" title=\"Delete record\"><button type=\"button\" class=\"btn btn-danger btn-sm\">Delete</button></a>";
			?>

	</div>
</div>
<br>
<br>
<br>
<br>
<?
	}
}
//Конец вывода всех событий







/*
	Блок вывода поиска по конкретной дате
*/

if($_GET['action'] == '' and $selectedDate != ''){
	/*1 вариант */
	//переводим полученую дату в метку времени
	$selected_date_start = strtotime($selectedDate);
	//получаем масив из метки времени
	$t = getdate($selected_date_start);
	$selected_date_end = mktime(23,59,59,$t['mon'],$t['mday']+1,$t['year']);
	$sql = "select * from reports where date>=('$selected_date_start') and date<('$selected_date_end')";
	$result = mysqli_query($link, $sql);
	$row = mysqli_fetch_assoc($result);
	$id = $row['id'];
	$date = date("d-m-Y",$row['date']);
	$content1 = nl2br($row['content1']);
	$content2 = nl2br($row['content2']);
	$content3 = nl2br($row['content3']);
	$content4 = nl2br($row['content4']);
echo <<<date
<div class="row">
	<div class="col-lg-12"><h3 class="page-header">$date</h3></div>
</div>
date;
if($content1 == NULL AND $content2 == NULL AND $content3 == NULL AND $content4 == NULL){
	Msg("There are no reports today.",3);
}
if($content1 != NULL){
echo <<<content1
<div class="row">
<div class="col-lg-12">
	<div class="panel panel-default">
		<div class="panel-heading">Network</div>
		<div class="panel-body">
			<p>$content1</p>
		</div>
	</div>
</div>
</div>
content1;
}

if($content2 != NULL){
echo <<<content2
<div class="row">
<div class="col-lg-12">
	<div class="panel panel-default">
		<div class="panel-heading">Clients</div>
		<div class="panel-body">
			<p>$content2</p>
		</div>
	</div>
</div>
</div>
content2;
}

if($content3 != NULL){
echo <<<content3
<div class="row">
 <div class="col-lg-12">
	<div class="panel panel-default">
		<div class="panel-heading">Change equipment</div>
		<div class="panel-body">
			<p>$content3</p>
		</div>
	</div>
</div>
</div>
content3;
}

if($content4 != NULL){
echo <<<content4
<div class="row">
<div class="col-lg-12">
	<div class="panel panel-default">
		<div class="panel-heading">Other</div>
		<div class="panel-body">
			<p>$content4</p>
		</div>
	</div>
</div>
</div>
content4;
}

}
//Конец вывода по дате.




//Блок поиска
if(isset($search) && ($search!='')){
	checkAccess(14);
	?>
<div class="row">
	<div class="col-md-6">
		<form role="form" action="" method="post">
			<div class="form-group input-group">
				<input type="text" name="search" value="<?=$search?>" class="form-control" placeholder="Search">
				<span class="input-group-btn">
					<button class="btn btn-default" type="submit" title="Click to search"><i class="fa fa-search"></i>
					</button>
				</span>
			</div>
		</form>
	</div>
	<div class="col-md-2">
		<!--<a href="?page=reports&action=add" title="Add new record"><button type="button" class="btn btn-success">Add new</button></a>-->
	</div>
	<!-- Поиск по конкретной дате
	<div class="col-md-4">
		<form name="sampleform" action="" method="post">
		<input hidden type="text" name="firstinput" size=20><a href="javascript:showCal('Calendar1')"><button type="button" class="btn btn-default">Select Date</button></a>
		<button type="submit" class="btn btn-default">Show Date</button>
		</form>
	</div>
	-->
</div>	
	<?php
	$sql = "SELECT * FROM reports WHERE content1 LIKE '%$search%' OR content2 LIKE '%$search%' OR content3 LIKE '%$search%' OR content4 LIKE '%$search%' ORDER BY date DESC";
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
		$content2 = highlight($search, nl2br($row['content2']));
		$content3 = highlight($search, nl2br($row['content3']));
		$content4 = highlight($search, nl2br($row['content4']));
	?>
	<div class="row">
		<div class="col-lg-12">
			<h3 class="page-header"><?=$date?></h3>
		</div>
		<div class="col-lg-12">
				<div class="row">
					<div class="col-lg-6">
						<div class="well well-sm">
							<h4>Network</h4>
							<p><?=$content1;?></p>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="well well-sm">
							<h4>Clients</h4>
							<p><?=$content2;?></p>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-6">
						<div class="well well-sm">
							<h4>Change equipment</h4>
							<p><?=$content3;?></p>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="well well-sm">
							<h4>Other</h4>
							<p><?=$content4;?></p>
						</div>
					</div>
				</div>
				<a href="../index.php?page=reports&action=edit&id=<?=$id?>"><button class="btn btn-primary btn-xs">Edit</button></a>
				<?php
					if($_SESSION['userAccess'] == '15')	echo "<a href=\"?page=reports&action=delete&id=$id\" title=\"Delete record\"><button type=\"button\" class=\"btn btn-danger btn-sm\">Delete</button></a>";
				?>

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


//Блок добавление нового события
if($_GET['action'] == 'add'){
	checkAccess(14);
	if($_POST['do'] == '')
	{
	//Выводим форму Добавления в БД
	?>
	<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
		<div class="panel-heading">Create report</div>
		<div class="panel-body">
	
		<div class="row">
		<div class="col-lg-8">
		
		<form role="form" action="" method="post">
			<div class="form-group">
				<textarea class="form-control" rows="7" name="content1" placeholder="Зміни в топології, Забанені\Розбанені порти, Флуд, т.д."></textarea>
			</div>
			<div class="form-group">
				<textarea class="form-control" rows="7" name="content2" placeholder="Бізнес клієнти, дзвінки, скарги, проблеми і рішення"></textarea>
			</div>
			<div class="form-group">
				<textarea class="form-control" rows="7" name="content3" placeholder="Заміна свічів"></textarea>
			</div>
			<div class="form-group">
				<textarea class="form-control" rows="7" name="content4" placeholder="Інші події за день(хто для кого шо залишав, прийшов Адмінчик переткнув шось і т.д.)"></textarea>
			</div>
		<input type="submit" value="Create Report" name="do" class="btn btn-primary">				
		</form>
		
		</div>
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
		if($_SERVER['REQUEST_METHOD'] == 'POST')
		{
			$date= time();
			$date = strftime("%d-%m-%y", $date);
			//Запрос для опеределения последней записи в БД
			$sql = " select date from reports order by id desc limit 1";
			$result = mysqli_query($link, $sql);
			$row = mysqli_fetch_assoc($result);
			$d = strftime("%d-%m-%y", $row['date']);
			//Если последння запись с датой равна сегодняшней дате, то вы водим сообщение, и запись не добаляем.
			if($date == $d){
				errorMsg("The record already exists in the database.");
			}else{
				$date= time();
				$user_id = $_SESSION[userId];
				$content1 = clearForDB($_POST['content1']);
				$content2 = clearForDB($_POST['content2']);
				$content3 = clearForDB($_POST['content3']);
				$content4 = clearForDB($_POST['content4']);
			
				//Запрос на добавление записи в бд.
				$sql = "INSERT INTO reports (date, user_id, content1, content2, content3, content4) VALUES (?, ?, ?, ?, ?, ?)";
				if(!$stmt = mysqli_prepare($link, $sql)){
					die("Error.");
				}
				mysqli_stmt_bind_param($stmt,"iissss",$date, $user_id, $content1, $content2, $content3, $content4);
				if(mysqli_stmt_execute($stmt)){
					successMsg('New event added successfully. <a href="index.php?page=reports" class="alert-link"> All reports.</a>');
				}
				mysqli_stmt_close($stmt);		
				//header("Location: index.php?page=reports");
				//header("Location: ".$_SERVER["PHP_SELF"]);
				//exit;
			}
		}
	}
}



?>