<?
// проверяем уроень доступа.
checkAccess(10);
$action = $_GET['action'];
if($action == 'add'){
	checkAccess(14);
	if($_POST['do'] == ''){
	//Выводим форму Добавления в БД.
	?>	
	<div class="row">
	<div class="col-lg-12">
	<div class="panel panel-default">
	<div class="panel-heading">Adding a new client.</div>
	<div class="panel-body">
		<div class="row">
		<div class="col-lg-8">
		<form role="form" action="" method="post">
			<div class="form-group">
			<input class="form-control" name="cn" placeholder="Contract number">
			</div>
			<div class="form-group">
			<input class="form-control" name="login" placeholder="Login">
			</div>
			<div class="form-group">
			<input class="form-control" name="address" placeholder="Address">
			</div>
			<div class="form-group">
			<input class="form-control" name="sw_ip" placeholder="Switch IP">
			</div>
			<div class="form-group">
			<input class="form-control" name="sw_port" placeholder="Switch PORT">
			</div>
			<div class="form-group">
			<input class="form-control" name="sw_vlan" placeholder="Switch VLAN">
			</div>
			<div class="form-group">
			<input class="form-control" name="ip_address" placeholder="ip-address">
			</div>
			<div class="form-group">
				<label>Location</label>
				<select class="form-control" name="client_location">
					<option></option>
					<option>nix</option>
					<option>billing</option>
				</select>
			</div>		
			<div class="form-group">
			<input class="form-control" name="client_id" placeholder="Client ID">
			<small>For nix: 'bus', 'fiz' or 'spb'.</small><br>
			<small>For billing: client 'id'.</small>
			</div>
			<input type="submit" value="Add" name="do" class="btn btn-success">				
			</div>
		</form>
		</div>
	</div>
	</div>
	</div>
	</div>
	<?php
	}
else{
	// Тут мы добавляем запись в БД
	$cn = $_POST['cn'];
	$login = $_POST['login'];
	$address = $_POST['address'];
	$sw_ip = $_POST['sw_ip'];
	$sw_port = $_POST['sw_port'];
	$sw_vlan = $_POST['sw_vlan'];
	$ip_address = $_POST['ip_address'];
	$client_location = $_POST['client_location'];
	$client_id = $_POST['client_id'];
	
	if($login != '' && $address != ''){
		$sql = "INSERT INTO clients (cn, login, address, sw_ip, sw_port, sw_vlan, ip_address, client_location, client_id)
				VALUES ('$cn', '$login', '$address', '$sw_ip', '$sw_port', '$sw_vlan', '$ip_address', '$client_location', '$client_id')";
		mysqli_query($link, $sql);
		header("Location: index.php?page=clients");
	}else{
		echo '<h1>Не все поля заполнены</h1>';
		if($login == ''){
			errorMsg("Поле login пустое");
		}
		if($address == ''){
			errorMsg("Поле address пустое");
		}		
	}
	}	
}

//выводим клиентов для просмотра
if($action == 'view'){
	checkAccess(10);
	if($_POST['do'] == ''){
	//Выводим форму промотра
	$id = $_GET['id'];
	$sql = "SELECT * FROM clients WHERE id='$id'";
	$result = $link->query($sql);
	$row_cnt = $result->num_rows;
	if($row_cnt > 0){
	$row = $result->fetch_assoc();
	$cn = $row['cn'];
	$login = $row['login'];
	$address = $row['address'];
	$sw_ip = $row['sw_ip'];
	$sw_port = $row['sw_port'];
	$sw_vlan = $row['sw_vlan'];
	$ip_address = $row['ip_address'];
	$client_location = $row['client_location'];
	$client_id = $row['client_id'];
	?>	
	<div class="row">
	<div class="col-lg-10">
	<div class="panel panel-default">
	<div class="panel-heading">View</div>
	<div class="panel-body">
		<div class="row">
		<div class="col-lg-8">
		<form role="form" action="" method="post">
			<div class="form-group">
			<label>Contract number</label>
			<input class="form-control" name="cn" placeholder="Contract number" value="<?=$cn?>">
			</div>
			<div class="form-group">
			<label>Login</label>
			<input class="form-control" name="login" placeholder="Login" value="<?=$login?>">
			</div>
			<div class="form-group">
			<label>Address</label>
			<input class="form-control" name="address" placeholder="Address" value="<?=$address?>">
			</div>
			<div class="form-group">
			<label>Switch ip-address</label>
			<input class="form-control" name="sw_ip" placeholder="Switch IP" value="<?=$sw_ip?>">
			</div>
			<div class="form-group">
			<label>Switch port</label>
			<input class="form-control" name="sw_port" placeholder="Switch PORT" value="<?=$sw_port?>">
			</div>
			<div class="form-group">
			<label>Vlan</label>
			<input class="form-control" name="sw_vlan" placeholder="Switch VLAN" value="<?=$sw_vlan?>">
			</div>
			<div class="form-group">
			<label>Client ip-address</label>
			<input class="form-control" name="ip_address" placeholder="ip-address" value="<?=$ip_address?>">
			</div>
			<div class="form-group">
				<label>Location</label>
				<select class="form-control" name="client_location">
				<?
					if($client_location == 'nix'){
						echo '<option></option>
						<option selected>nix</option>
						<option>billing</option>';
					}elseif($client_location == 'billing'){
						echo '<option></option>
							<option>nix</option>
							<option selected>billing</option>';
					}else{
						echo 'Unknown';
					}
					$client_id = $row['client_id'];
					if($client_location == 'nix'){
						$href = "http://77.87.152.2/admin/in.fcgi?valw=$client_id&show=1&client=$login";
						$location = "nix";
					}elseif($client_location == 'billing'){
							$location = "billing";
							$href = "https://billing.airbites.net.ua/clients/index.cgi?View=SEE&Name=$client_id";
					}else{
						$location = "Unknown";
					}
				?>
				</select>
			</div>		
			<div class="form-group">
			<label>Client ID</label>
			<input class="form-control" name="client_id" placeholder="Client ID" value="<?=$client_id?>">
			</div>
			</div>
		</form>
		</div>
	</div>
	</div>
	</div>
	<div class="col-lg-2">
		<a href="<?=$href?>" target="_blank"><button type="button" class="btn btn-primary"><?=$location?></button></a><br><br>
		<a href="index.php?page=clients&action=edit&id=<?=$id?>"><button type="button" class="btn btn-primary">Edit</button></a><br><br>
		<a href="index.php?page=clients&action=del&id=<?=$id?>"><button type="button" class="btn btn-danger" onclick="return confirm('Вы действительно хотите удалить эту запись?'); ">Delete</button></a><br><br>
	</div>
	</div>
	<?php
	}else{
		errorMsg("Unknown ID. ");
	}	
	}//Условие "do"
}



//выводим клиентов для редактирования
if($action == 'edit'){
	checkAccess(14);
	if($_POST['do'] == ''){
	//Выводим форму Добавления в БД.
	$id = $_GET['id'];
	$sql = "SELECT * FROM clients WHERE id='$id'";
	$result = $link->query($sql);
	$row = $result->fetch_assoc();
	$cn = $row['cn'];
	$login = $row['login'];
	$address = $row['address'];
	$sw_ip = $row['sw_ip'];
	$sw_port = $row['sw_port'];
	$sw_vlan = $row['sw_vlan'];
	$ip_address = $row['ip_address'];
	$client_location = $row['client_location'];
	$client_id = $row['client_id'];
	?>	
	<div class="row">
	<div class="col-lg-12">
	<div class="panel panel-default">
	<div class="panel-heading">Edit</div>
	<div class="panel-body">
		<div class="row">
		<div class="col-lg-8">
		<form role="form" action="" method="post">
		
		<div class="form-group">
		<input class="form-control" name="cn" placeholder="Contract number" value="<?=$cn?>">
		</div>
		
		<div class="form-group">
		<input class="form-control" name="login" placeholder="Login" value="<?=$login?>">
		</div>
		
		
		<div class="form-group">
		<input class="form-control" name="address" placeholder="Address" value="<?=$address?>">
		</div>

		<div class="form-group">
		<input class="form-control" name="sw_ip" placeholder="Switch IP" value="<?=$sw_ip?>">
		</div>
		
		<div class="form-group">
		<input class="form-control" name="sw_port" placeholder="Switch PORT" value="<?=$sw_port?>">
		</div>
		
		<div class="form-group">
		<input class="form-control" name="sw_vlan" placeholder="Switch VLAN" value="<?=$sw_vlan?>">
		</div>
		
		<div class="form-group">
		<input class="form-control" name="ip_address" placeholder="ip-address" value="<?=$ip_address?>">
		</div>
		
		
		<div class="form-group">
			<label>Location</label>
			<select class="form-control" name="client_location">
			<?
				if($client_location == 'nix'){
					echo '<option></option>
					<option selected>nix</option>
					<option>billing</option>';
				}elseif($client_location == 'billing'){
					echo '<option></option>
						<option>nix</option>
						<option selected>billing</option>';
				}else{
					echo 'Unknown';
				}
			?>

			</select>
		</div>		
		
		<div class="form-group">
		<input class="form-control" name="client_id" placeholder="Client ID" value="<?=$client_id?>">
		</div>
		<input value="<?=$id?>" name="id" hidden>
		<input type="submit" value="Save" name="do" class="btn btn-primary">				
		</div>
		</form>
		</div>
	</div>
	</div>
	</div>
	</div>
	<?php
	}
	else{
		// Тут мы обновляем запись в БД
		$id = $_POST['id'];
		$cn = $_POST['cn'];
		$login = $_POST['login'];
		$address = $_POST['address'];
		$sw_ip = $_POST['sw_ip'];
		$sw_port = $_POST['sw_port'];
		$sw_vlan = $_POST['sw_vlan'];
		$ip_address = $_POST['ip_address'];
		$client_location = $_POST['client_location'];
		$client_id = $_POST['client_id'];
		$sql = "UPDATE clients SET
						cn='$cn',
						login='$login',
						address='$address',
						sw_ip='$sw_ip',
						sw_port='$sw_port',
						sw_vlan='$sw_vlan',
						ip_address='$ip_address',
						client_location='$client_location',
						client_id='$client_id'
			WHERE id='$id'";
		$link->query($sql);		
		$link->close();
		header("Location: index.php?page=clients");
	}	
}

//выводим клиентов для удаления
if($action == 'del'){
	checkAccess(14);
	$id = $_GET['id'];
	$sql = "DELETE FROM clients WHERE id='$id'";
	if ($link->query($sql) === TRUE) {
		successMsg("Record deleted successfully");
	} else {
		errorMsg("Error deleting record: ");
		$link->error;
	}
	$link->close();
}

//выводим список всех клиентов из БД.
if($action == ''){
	checkAccess(10);
?>
<div class="row">
	<div class="col-lg-12">
		<a href="index.php?page=clients&action=add"><button type="button" class="btn btn-success">ADD</button></a>
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
								<th>CN</th>
								<th>Login</th>
								<th>Address</th>
								<th>Switch ip</th>
								<th>Port</th>
								<th>Vlan</th>
								<th>Ip</th>
								<th>Location</th>
							</tr>
						</thead>
						<tbody>
						<?
						$sql = "SELECT * FROM clients";
						$result = $link->query($sql);
						while($row = $result->fetch_assoc()){
							$id = $row['id'];
							$cn = $row['cn'];
							$login = $row['login'];
							$address = $row['address'];
							$sw_ip = $row['sw_ip'];
							$sw_port = $row['sw_port'];
							$sw_vlan = $row['sw_vlan'];
							$ip_address = $row['ip_address'];
							$client_location = $row['client_location'];
							$client_id = $row['client_id'];
							if($client_location == 'nix'){
								$location = "<a href=\"http://77.87.152.2/admin/in.fcgi?valw=$client_id&show=1&client=$login\" target=\"_blank\">nix</a>";
							}elseif($client_location == 'billing'){
								$location = "<a href=\"https://billing.airbites.net.ua/clients/index.cgi?View=SEE&Name=$client_id\" target=\"_blank\">billing</a>";
							}else{
								$location = "Unknown";
							}
						?>
							<tr class="odd gradeX" onclick="window.location.href='index.php?page=clients&action=view&id=<?=$id?>'" style='cursor:pointer'>
								<td><?=$cn?></td>
								<td><?=$login?></td>
								<td><?=$address?></td>
								<td><?=$sw_ip?></td>
								<td><?=$sw_port?></td>
								<td><?=$sw_vlan?></td>
								<td><?=$ip_address?></td>
								<td><?=$location?></td>
							</tr>
						<?
						}//конец цыкла.
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