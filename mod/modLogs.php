<?php
checkAccess(14);
$search = $_POST['search'];
//кол-во записей в таблице.
$sql = "SELECT id FROM logs_ports_action";
$result = $link->query($sql);
$row_cnt = $result->num_rows;
$result->close();

//кол-во записей в таблице за сегодня.
//получаем масив из метки времени
$t = getdate(time());
$selected_date_start = mktime(0,0,0,$t['mon'],$t['mday'],$t['year']);
$selected_date_end = time();
$sql = "select id from logs_ports_action where date>=('$selected_date_start') and date<('$selected_date_end')";
$result = $link->query($sql);
$row_cnt_today = $result->num_rows;
$result->close();

//кол-во записей в таблице за вчера.
//получаем масив из метки времени
$t = getdate(time());
//print_r($t);
$selected_date_start = mktime(0,0,0,$t['mon'],$t['mday']-1,$t['year']);
$selected_date_end = mktime(23,59,0,$t['mon'],$t['mday']-1,$t['year']);
$sql = "select id from logs_ports_action where date>=('$selected_date_start') and date<('$selected_date_end')";
$result = $link->query($sql);
$row_cnt_yesterday = $result->num_rows;
$result->close();
?>
<div class="row">
<div class="col-lg-12">
<div class="panel panel-default">
	<div class="panel-heading">
	<form action="../index.php?page=logs" method="post">
	<input type="text" name="search" value="<?=$search?>">
	<input type="submit" value="Search">
	<form>
	All: <code><?=$row_cnt;?></code> records. Today: <code><?=$row_cnt_today;?></code> Yesterday: <code><?=$row_cnt_yesterday;?></code>
	</div>
<div class="panel-body">


	<?php
	//Если строка поиска пуста выводим всю таблицу логов
	if(empty($search)){
		$sql = "SELECT * FROM logs_ports_action ORDER BY date DESC LIMIT 50";
	}
	//Если задана искомая строка производим поиск в БД.
	if(!empty($search)){
		$sql = "SELECT * FROM logs_ports_action WHERE ip_address LIKE '%$search%' OR action LIKE '%$search%' ORDER BY date DESC";
	}
	
	
	$result = $link->query($sql);

	if($result->num_rows > 0)
	{
	?>
	<div class="table-responsive">
		<table class="table table-hover">
		<thead>
			<tr>
			<th width="200px">date</th>
			<th width="200px">user</th>
			<th width="150px">ip-address</th>
			<th>action</th>
			</tr>
		</thead>

		<tbody>	
	<?php
		while($row = $result->fetch_assoc()) {
			//вытягиваем имя пользователя.
			$id = $row['user_id'];
			$s = "SELECT `login` FROM users WHERE id='$id'";
			$r = $link->query($s);
			$user = $r->fetch_assoc();
			//имя пользователя.
			$user = $user['login'];
			
			$date = strftime("%d-%b-%Y %R", $row['date']);
			$ip_address = $row['ip_address'];
			$port = $row['port'];
			$action = $row['action'];
			
			switch($action){
				case 'enable': $color = '#238025'; break;
				case 'disable': $color = '#bf0505'; break;
				case 'auto': $color = '#12199d'; break;
				case '100_full': $color = '#12199d'; break;
				case '10_full': $color = '#12199d'; break;
				case 'reconnect': $color = '#de752a'; break;
				default : $color = "";					
			}
			
			echo "
			<tr>
			<td>$date</td>
			<td><a target=\"_blank\" href=\"index.php?page=user&action=edit&id=$id\">$user</a></td>
			<td><a href=\"../?ip=$ip_address\">$ip_address</a></td>
			<td><font color=\"$color\">$port port {$action}</color></td>
			</tr>";			
		}
	?>
		</tbody>
	</table>
	</div>
	<?php
	}
	else
	{
		echo "<h2>0 results</h2>";
	}
	
	$link->close();
	?>




</div>

</div>
</div>
</div>