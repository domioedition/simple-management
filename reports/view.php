<?
// session_start();
// include '../inc/conf.php';
// include '../inc/functions.php';
// include 'reports.class.php';
// include '../inc/head.inc.php';
	checkAccess(14);
	$id = $_GET['id'];
	$report = new Reports();
	$sql = "SELECT
		-- reportsNew.id,
		reportsNew.date,
		reportsNew.user_id,
		reportsNew.category,
		reportsNew.content,
		users.id,
		users.username 
	FROM reportsNew, users WHERE reportsNew.user_id = users.id AND reportsNew.id='$id'";
	$result = $report->getRows($sql);
	$result = $result[0];
	// print_r($result);
	if($result == 0){
		errorMsg("Unknown ID.");
		die("Sorry.");
	}
		//Подготавливаем перменные для вывода.
		$userIdCreator = $result['user_id'];
		$date = date("d-m-y H:i",$result['date']);
		$username =  $result['username'];
		$category = $result['category'];
		$content = nl2br($result['content']);
?>


<div class="row">
<div class="col-lg-8">
<div class="panel panel-info">
    <div class="panel-heading"><?=$date;?> - <?=$username;?></div>
    <!-- /.panel-heading -->
    <div class="panel-body">
		<p><?=$content?></p>
        <!-- Modal -->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<form method="post" action="edit.php" role="form">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">Редактирование</h4>
                    </div>
                    <div class="modal-body">

			<div class="form-group">
				<label>Категория</label>
				<select name="category" class="form-control">
					<option value="1">Network</option>
					<option value="2">Clients</option>
					<option value="3">Equipment</option>
					<option value="4">Other</option>
				</select>
			</div>
				<div class="form-group">
					<label>Запись</label>
					<textarea class="form-control" name="content" rows="10"><?=$content?></textarea><br>
					<input type="text" name="id" value="<?=$id;?>" hidden>
					<input type="text" name="userIdCreator" value="<?=$userIdCreator;?>" hidden>
				</div>
				<div class="form-group">
				</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
			</form>
        </div>
        <!-- /.modal -->
    </div>
    <!-- .panel-body -->
    <div class="panel-footer">
		<!-- Button trigger modal -->
		<button class="btn btn-primary btn-xs" data-toggle="modal" data-target="#myModal">Редактировать</button>
		<a href="delete.php?id=<?=$id;?>" onclick="return confirm('Вы действительно хотите удалить эту запись?');"><button type="button" class="btn btn-danger btn-xs">Удалить</button></a>
    </div>    
</div>
<!-- /.panel -->
</div>
<!-- /.col-lg-6 -->
</div>
<!-- /.row -->



<div class="row">
	<div class="col-lg-6">
		<h3 class="page-header">Comments</h3>
	</div>
</div>
<div class="row">
	<div class="col-lg-12">
	<?
	// $sql = "SELECT * FROM reports_comment WHERE report_id='$id'";
	$sql = "SELECT reports_comment.id, reports_comment.report_id, reports_comment.date, reports_comment.user_id,users.id, reports_comment.comment, users.username
			FROM reports_comment, users WHERE reports_comment.user_id = users.id AND reports_comment.report_id='$id'";
	$result = $link->query($sql);
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			// print_r($row);
			$date = date("d-m-Y H:i", $row['date']);
			$username = $row['username'];
			$comment = $row['comment'];
		echo '<p><blockquote><p>'.$comment.'</p><small>'.$date.' - '. $username.'</small></blockquote></p>'."\n\n";
		}
	}
	?>
	</div>
</div>

<div class="row">
	<div class="col-lg-6">
	<form action="../reports/addComment.php" method="post">
	<div class="form-group">
		<input type="text" name="id" value="<?=$id;?>" hidden>
		<!-- <label>Добавить комментарий</label> -->
		<textarea class="form-control" rows="3" name="comment"></textarea>
	</div>
	<div class="form-group">
		<button type="submit" class="btn btn-default">Оставить комментарий</button>
	</div>
	</form>
	</div>
</div>

