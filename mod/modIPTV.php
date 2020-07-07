<?






//IPTV модуль

// проверяем уроень доступа.
checkAccess(14);

//Џолучаем переменную действие, чтобы знать что показывать на странице.
//$action = $_GET['action'];

//выводим список всех IPTV каналов из БД.
if($action == ''){
?>
<div class="row">
	<div class="col-lg-12">
		<a href="index.php?page=iptv&action=add"><button type="button" class="btn btn-success">ADD</button></a>
	</div>
	<br><br>
</div>
	
<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				DataTables Clients
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="dataTable_wrapper">
					<table class="table table-striped table-bordered table-hover" id="dataTables-example">
						<thead>
							<tr>
								<th>name</th>
								<th>ip_address</th>
								<th>status</th>
								<th>comments</th>
								<th>troubles</th>
							</tr>
						</thead>
						<tbody>
						<?
						$sql = "SELECT * FROM iptv";
						$result = $link->query($sql);
						while($row = $result->fetch_assoc()){
							$id = $row['id'];
							$name = $row['name'];
							$ip_address = $row['ip_address'];
							$status = $row['status'];
							if($status == '1')$status = "<button type=\"button\" class=\"btn btn-success btn-xs\">Active</button>";
							if($status == '0')$status = "<button type=\"button\" class=\"btn btn-danger btn-xs\">Not Active</button>";
							$comments = $row['comments'];
							$troubles = $row['troubles'];							
						?>
							<tr class="odd gradeX" onclick="window.location.href='index.php?page=iptv&action=view&id=<?=$id?>'" style='cursor:pointer'>
								<td><?=$name?></td>
								<td><?=$ip_address?></td>
								<td><?=$status?></td>
								<td><?=$comments?></td>
								<td><?=$troubles?></td>
							</tr>
						<?
						}//конец цикла.
						?>	
						</tbody>
					</table>
				</div>
				<!-- /.table-responsive -->
			</div>
			<!-- /.panel-body -->
		</div>
		<!-- /.panel -->
	</div>
	<!-- /.col-lg-12 -->
</div>
<?	
}
?>





