<div class="container">
  <div>
	<span>Username:</span>
	<a href="#" id="username" data-type="text" data-placement="right" data-title="Enter username">superuser</a>
  </div>
  
  <div>
	<span>Status:</span>
	<a href="#" id="status"></a>
  </div>
</div>


<?php

//Модуль управления пользователями
if($_SERVER["REQUEST_METHOD"] == 'POST'){
//	$name = $_POST['name'];
//	$content = $_POST['content'];
}

if($_SERVER["REQUEST_METHOD"] == 'GET'){
	//$action = $_GET['action'];
}?>







<div class="row">
	<div class="col-lg-8">
<?php



//Показываем историю авторизации в системе
if($_GET['action'] == 'history'){
	checkAccess(14);
	$sql = "SELECT * FROM users, loginfrom WHERE users.id=loginfrom.user_id  ORDER BY date DESC LIMIT 0,20";
	$result = mysqli_query($link, $sql) or die(mysqli_error($link));
	echo '<div class="table-responsive">
	<table class="table table-hover">
	<thead>
	<th>login</th>
	<th>time</th>
	<th>from</th>
	</thead>
	<tbody>';
	while($row = mysqli_fetch_assoc($result)){
		$login = $row['login'];
		$date = date('d-M-Y H:i', $row['date']);
		$ip_address = $row['ip_address'];
	echo <<<LINE
	<tr>
	<td>$login</td>
	<td>$date</td>
	<td>$ip_address</td>
	<tr>
LINE;
	}
	echo '</tbody>
	</table>
	</div>';

}


//Вывод всех пользователей
if($_GET['action'] == '')
{
	checkAccess(14);
	echo '<div class="table-responsive">
	<table class="table table-hover">
	<thead>
	<tr>
	<th>login</th>
	<th>access level</th>
	<th>status</th>
	</tr>
	</thead>
	<tbody>
	';	
	$sql = "SELECT * FROM users ORDER BY login";
	$result = mysqli_query($link, $sql);
	while($row=mysqli_fetch_assoc($result))
	{
	$login = $row['login'];
	$access = $row['access'];
	$status = $row['status'];
	if($status == 0){
		$status = "Не активный";
	}else{
		$status = "Активный";
	}
	echo <<<LINE
	<tr onclick="window.location.href='?page=user&action=edit&id=$row[id]'">
		<td>$login</td>
		<td>$access</td>
		<td>$status</td>
	</tr>
LINE;
		}
	echo '</tbody>
	</table>
	</div>';	
}
	
	

//добавление нового пользователя
if($_GET['action'] == 'register'){
	checkAccess(14);
	if (empty($_POST)){
	?>
	<div class="row">

	<div class="col-lg-12">
	<div class="panel panel-default">
	<div class="panel-heading">Register new user</div>
	<div class="panel-body">

	<div class="col-lg-8">
	<form role="form" action="" method="post">
		<div class="form-group">
		<input class="form-control" name="login" placeholder="login">
		</div>

		<div class="form-group">
		<input class="form-control" name="username" placeholder="username">
		</div>

		<div class="form-group">
		<input class="form-control" name="email" placeholder="email">
		</div>
		
		<div class="form-group">
		<input class="form-control" type="password" name="password" placeholder="password">
		</div>

		<div class="form-group">
		<input class="form-control" name="access" placeholder="access-level">
		<p class="help-block">access-level: от 1 до 15. Чем выше access-level, тем больше прав у пользователя.</p>
		</div>

		<div class="form-group">
		<button class="btn btn-primary" type="submit">Register</button>
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
		// обрабатывае пришедшие данные функцией mysql_real_escape_string перед вставкой в таблицу БД
		
		$login = (isset($_POST['login'])) ? mysqli_real_escape_string($link, $_POST['login']) : '';
		$username = (isset($_POST['username'])) ? mysqli_real_escape_string($link, $_POST['username']) : '';
		$email = (isset($_POST['email'])) ? mysqli_real_escape_string($link, $_POST['email']) : '';
		$password = (isset($_POST['password'])) ? mysqli_real_escape_string($link, $_POST['password']) : '';
		$access = (isset($_POST['access'])) ? mysqli_real_escape_string($link, $_POST['access']) : '';
		
		
		// проверяем на наличие ошибок (например, длина логина и пароля)
		
		$error = false;
		$errort = '';
		
		if (strlen($login) < 2)
		{
			$error = true;
			$errort .= 'Длина логина должна быть не менее 2х символов.<br />';
		}
		if (strlen($password) < 6)
		{
			$error = true;
			$errort .= 'Длина пароля должна быть не менее 6 символов.<br />';
		}
		
		// проверяем, если юзер в таблице с таким же логином
		$query = "SELECT `id`
					FROM `users`
					WHERE `login`='{$login}'
					LIMIT 1";
		$sql = mysqli_query($link, $query) or die(mysqli_error());
		if (mysqli_num_rows($sql)==1)
		{
			$error = true;
			$errort .= 'Пользователь с таким логином уже существует в базе данных, введите другой.<br />';
		}
		
		
		// если ошибок нет, то добавляем юзаре в таблицу
		
		if (!$error)
		{
			// генерируем соль и пароль
			
			$salt = GenerateSalt();
			$hashed_password = md5(md5($password) . $salt);
			$query = "INSERT
						INTO `users`
						SET
							`login`='{$login}',
							`password`='{$hashed_password}',
							`salt`='{$salt}',
							`username`='{$username}',
							`email`='{$email}',
							`access`='{$access}'
							";
			$sql = mysqli_query($link, $query) or die(mysqli_error());
			
			successMsg("Congratulations to the new user has been successfully registered. <p><a href=\"?page=user\">List </a>of users.</p>");
		}
		else
		{
			print '<h3>Возникли следующие ошибки</h3>' . $errort;
		}
	}
}
	
	
	
	
//Редактирование пользователя
if($_GET['action'] == 'edit'){
	$id = (int)$_GET['id'];
	if($_SESSION['userAccess']>=14){
		$sql = "SELECT id, login, username, email, access, status FROM users WHERE users.id=$id LIMIT 1";
		$result = mysqli_query($link, $sql);
		if(mysqli_num_rows($result) == 1){
		$row = mysqli_fetch_array($result);
		$login = $row['login'];
		$username = $row['username'];
		$email = $row['email'];
		$access = $row['access'];
		$status = $row['status'];
		if($status == 0){
			$status = "Не активный";
		}else{
			$status = "Активный";
		}	
		echo '
		<div class="table-responsive">
		<table class="table table-striped table-bordered table-hover">
		<tbody>
		</tbody>
		<tr><td>login</td><td><code>'.$login.'</code></td></tr>
		<tr><td>username</td><td><code>'.$username.'</code></td></tr>
		<tr><td>email</td><td><code>'.$email.'</code></td></tr>
		<tr><td>access level</td><td><code>'.$access.'</code></td></tr>
		<tr><td>status</td><td><code>'.$status.'</code></td></tr>
		</table>
		<p>
			<a href="?page=user&action=deAct&id='.$id.'"><button type="button" class="btn btn-info">Activate/Deactivate</button></a>
			<a href="?page=user&action=changepass&id='.$id.'"><button type="button" class="btn btn-primary">Change password</button></a>
			<a href="?page=user&action=delete&id='.$id.'"><button type="button" class="btn btn-danger">Remove</button></a>
		</p>
		</div>
		';
		}
		else{
			errorMsg("Установлен неверный ID пользователя.");
		}
	}
	else{
		echo "<p>Здесь Вы можете изменить свой пароль. Достаточно нажать на кнопку.</p>";
		echo '<p><a href="?page=user&action=changepass&id='.$id.'"><button type="button" class="btn btn-primary">Change password</button></a></p>';
	}
}


//Удаление пользователя
if($_GET['action'] == 'delete'){
	checkAccess(15);
	if(isset($_GET['id'])){
		$id = (int)$_GET['id'];
		$sql = "DELETE FROM users WHERE users.id = $id";
		if($id == 1)
		{
			echo "<h4>Нельзя удалить <code>admin</code>пользователя.</h4>";
		}
		else
		{
			if($result = mysqli_query($link, $sql))
			{
				header( "refresh:5;url=?page=user" );
				successMsg('User was successfully deleted!<p>You\'ll be automatically redirected to the main Node page in about 5 secs. If not, click <a href="?page=user">here</a>.</p>');
				exit;
			}
		}
	}else{
		errorMsg("Не установлен ID пользователя.");
	}
}





//Меняем статус пользователя. Активный/Не Активный
if($_GET['action'] == 'deAct'){
	checkAccess(14);
	$id = (int)$_GET['id'];
	if($id == '1'){
		errorMsg("Нельзя деактивировать пользователя <code>admin</code>.");
		exit;
	}
	if($id == $_SESSION['userId']){
		errorMsg("Нельзя деактивировать самого себя.");
		exit;
	}
	$sql = "SELECT status FROM users WHERE users.id = $id LIMIT 1";		
	$result = mysqli_query($link, $sql);
	if(mysqli_num_rows($result) == 1){
		$row = mysqli_fetch_array($result);
		$status = $row['status'];
		if($status == 0){
			$sql = "UPDATE users SET status='1' WHERE id='$id'";
		}else{
			$sql = "UPDATE users SET status='0' WHERE id='$id'";
		}
		$result = mysqli_query($link, $sql);
		header("refresh:0;url=../index.php?page=user&action=edit&id=$id" );
		exit;
	}else{
		errorMsg("Установлен неверный ID пользователя.");
	}
}




//Изменение пароля
if($_GET['action'] == 'changepass'){
	if(!$_POST['do'])
	{
		$id = $_GET['id'];
		if($id == 1) die("<h4>Нельзя изменить пароль <code>admin</code> пользователя.</h4>");
		echo '
		<h4>Change Password</h4>
		<p>Введите новый пароль.</p>
		<form role="form" action="?page=user&action=changepass" method="post">
			
			<div class="form-group">
			<input class="form-control" type="password" name="password" placeholder="New password">
			</div>

			<div class="form-group">
			<input hidden name="do" value="do">
			<input hidden name="id" value="'.$id.'">
			<button class="btn btn-primary" type="submit">Change</button>
			</div>
		</form>		
		';
	}else{
		$id = (int)$_POST['id'];
		//Проверка на изменение пароля только для ссебя.
		if($id == $_SESSION['userId']){
		$password = $_POST['password'];
		$error = false;
		$errort = '';
		if (strlen($password) < 6)	{
			$error = true;
			$errort .= 'Длина пароля должна быть не менее 6 символов.<br />';
		}
		if (!$error){
			// генерируем соль и пароль
			
			$salt = GenerateSalt();
			$hashed_password = md5(md5($password) . $salt);
			$query = "UPDATE `users` SET `password`='{$hashed_password}', `salt`='{$salt}' WHERE `id`='{$id}'";
			if(mysqli_query($link, $query) or die(mysqli_error())){
				successMsg("Пароль успешно изменен.");
			}
			}
			else{
				print '<h3>Возникли следующие ошибки</h3>' . $errort;
			}
		}else{
			print '<h3>Пароль можно моменять только себе.</h3>';
	}
		
/* 		//Если все таки хотим поменять пароль не себе, нужно иметь уровень прав.
		if($id != $_SESSION['userId']){
			checkAccess(15);
			$password = $_POST['password'];
			$error = false;
			$errort = '';
			if (strlen($password) < 6){
				$error = true;
				$errort .= 'Длина пароля должна быть не менее 6 символов.<br />';
			}
			if (!$error){
				// генерируем соль и пароль
				$salt = GenerateSalt();
				$hashed_password = md5(md5($password) . $salt);
				$query = "UPDATE `users` SET `password`='{$hashed_password}', `salt`='{$salt}' WHERE `id`='{$id}'";
				if(mysqli_query($link, $query) or die(mysqli_error())){
					successMsg("Пароль успешно изменен.");
				}
			}
			else{
				print '<h3>Возникли следующие ошибки</h3>' . $errort;
			}
		}else{
			die("stop");
		} */
	}
}
?>
	</div>
	
	<? if($_SESSION['userAccess']>=14)
	{
	?>
	<div class="col-lg-4">
		<p align="right">
			<a href="?page=user&action=register"><button type="button" class="btn btn-primary">New</button></a>
			<a href="?page=user&action=history"><button type="button" class="btn btn-primary">History</button></a>
		</p>	
	</div>
	<?
	}
	?>
</div>

<!-- <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css" rel="stylesheet">
<script src="http://code.jquery.com/jquery-2.0.3.min.js"></script>
<script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/js/bootstrap.min.js"></script>  
<link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.4.6/bootstrap-editable/css/bootstrap-editable.css" rel="stylesheet"/>
<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.4.6/bootstrap-editable/js/bootstrap-editable.min.js"></script>
<script src="../mod/main.js"></script>
 -->