<?php
session_start();
error_reporting(E_ALL & ~E_NOTICE);

/*Подключаем файлы с конфигурацией MySQL и функциями*/
require('inc/conf.php');
require('inc/functions.php');

if (isset($_GET['logout']))
{
	session_destroy();	
	setcookie('login', '', 0, "/");
	setcookie('password', '', 0, "/");
	// и переносим его на главную
	header('Location: index.php');
	exit;
}

if (isset($_SESSION['userId']))
{
	// юзер уже залогинен, перекидываем его отсюда на закрытую страницу
	
	header('Location: index.php');
	exit;

}



if (!empty($_POST))
{
	$login = (isset($_POST['login'])) ? mysqli_real_escape_string($link,$_POST['login']) : '';
	
	$query = "SELECT `salt`
				FROM `users`
				WHERE `login`='{$login}'
				LIMIT 1";
	$sql = mysqli_query($link, $query) or die(mysqli_error());
	
	if (mysqli_num_rows($sql) == 1)
	{
		$row = mysqli_fetch_assoc($sql);
		
		// итак, вот она соль, соответствующая этому логину:
		$salt = $row['salt'];
		
		// теперь хешируем введенный пароль как надо и повторям шаги, которые были описаны выше:
		$password = md5(md5($_POST['password']) . $salt);
		
		// и пошло поехало...

		// делаем запрос к БД
		// и ищем юзера с таким логином и паролем

		$query = "SELECT `id`, `login`, `access`, `status`
					FROM `users`
					WHERE `login`='{$login}' AND `password`='{$password}'
					LIMIT 1";
		$sql = mysqli_query($link, $query) or die(mysqli_error());
		// если такой пользователь нашелся
		if (mysqli_num_rows($sql) == 1)
		{
			$row = mysqli_fetch_assoc($sql);
			
			// если такой пользователь нашелся и поле 'status' не  актвино редиректим назад его.
			$status = (int)$row['status'];
			if($status == '0'){
				header('Location: login.php');
				exit;
			}

			// то мы ставим об этом метку в сессии (допустим мы будем ставить ID пользователя)
			//записываем в сессию данные о пользователе
			$_SESSION['userId'] = $row['id'];
			$_SESSION['userLogin'] = $row['login'];
			$_SESSION['userAccess'] = $row['access'];
			
			
			// если пользователь решил "запомнить себя"
			// то ставим ему в куку логин с хешем пароля
			
			$time = 86400; // ставим куку на 24 часа
			
			if (isset($_POST['remember']))
			{
				setcookie('login', $login, time()+$time, "/");
				setcookie('password', $password, time()+$time, "/");
			}
			
			//делаем запись в БД о воемени, ip-address, login
			$userId = $row['id'];
			$date = time();
			$ipaddress = get_client_ip();
			$sql = "INSERT INTO loginfrom (user_id, date, ip_address) VALUES ('$userId', '$date', '$ipaddress')";
			mysqli_query($link, $sql) or die(mysqli_error($link));

			
			// и перекидываем его на закрытую страницу
			header('Location: index.php');
			exit;

			// не забываем, что для работы с сессионными данными, у нас в каждом скрипте должно присутствовать session_start();
		}
		else
		{
			$ipaddress = get_client_ip();
			//вызываем функицю для записи в БД о некорректных попытках авторизации.		
			loginError($login, $ipaddress);
			$log = "<code>Wrong login or password.</code>";
			header('Location: login.php');
			exit;
			#die('Такой логин с паролем не найдены в базе данных. И даём ссылку на повторную авторизацию. — <a href="login.php">Авторизоваться</a>');
		}
	}
	else
	{
		$ipaddress = get_client_ip();
		//вызываем функицю для записи в БД о некорректных попытках авторизации.		
		loginError($login, $ipaddress);		
		$log = "<code>Wrong login or password.</code>";
		header('Location: login.php');
		exit;
		//die('пользователь с таким логином не найден, даём ссылку на повторную авторизацию. — <a href="login.php">Авторизоваться</a>');
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SM::Simple Management::Sign In</title>

    <!-- Bootstrap Core CSS -->
    <link href="../bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="../bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="../bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">SM::Please Sign In</h3>
                    </div>
                    <div class="panel-body">
                        <form role="form" action="login.php" method="post">
                            <fieldset>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Login" name="login" type="text" autofocus>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" placeholder="Password" name="password" type="password" value="">
                                </div>
                                <!-- Change this to a button or input when using this as a form -->
								<button type="submit" class="btn btn-lg btn-success btn-block">Login</button>
                            </fieldset>
                        </form>
					<br><br><center><?=$log?></center>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- jQuery -->
    <script src="../bower_components/jquery/dist/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../bower_components/metisMenu/dist/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../dist/js/sb-admin-2.js"></script>

</body>

</html>