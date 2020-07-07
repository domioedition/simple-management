<?php
	define ("DB_HOST","localhost");
	define ("DB_LOGIN","admin");
	define ("DB_PASS","I18334oLWNc8");

	define ("DB_NAME","sm");


	$link = mysqli_connect(DB_HOST, DB_LOGIN, DB_PASS, DB_NAME) or die (mysqli_connect_error());
	mysqli_query($link, "SET NAMES utf8");
	mysqli_query($link, "SET CHARACTER SET 'utf8';"); 
	mysqli_query($link, "SET SESSION collation_connection = 'utf8_general_ci';"); 	
	
//    mysqli_query($link, "set character_set_client	='utf-8'");
//    mysqli_query($link, "set character_set_results	='utf-8'");
//    mysqli_query($link, "set collation_connection	='utf8_general_ci'");

    function slashes(&$el)
	{
		if (is_array($el))
			foreach($el as $k=>$v)
				slashes($el[$k]);
		else $el = stripslashes($el); 
    }
	
	if (ini_get('magic_quotes_gpc'))
	{
	    slashes($_GET);
	    slashes($_POST);    
	    slashes($_COOKIE);
	}
?>