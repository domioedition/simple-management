<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
<ul>
<?


// $file = file('mail/1.txt', FILE_USE_INCLUDE_PATH);
// foreach ($file as $line) {
// 	if (preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $line, $match)) {
// 		if (filter_var($match[0], FILTER_VALIDATE_IP)) {
//         	$block_list[] = $match[0];			
// 		}
// 	}
// }

// var_dump($block_list);




$filesArr = array();
if ($handle = opendir('mail')) {
/*    echo "Дескриптор каталога: $handle\n";
    echo "Записи:\n";*/
    while (false !== ($entry = readdir($handle))) {
        // echo "$entry\n";
        $filesArr[] = $entry;
    }
    closedir($handle);
}

foreach ($filesArr as $file_name) {
	$file_name = "mail/".$file_name;
	if ( is_file($file_name) ) {
		$file = file($file_name);
		foreach ($file as $line) {
			if (preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $line, $match)) {
				if (filter_var($match[0], FILTER_VALIDATE_IP)) {
					$block_list[] = $match[0];			
				}
			}
		}
	}
}

// print_r($block_list);
$block_list = array_count_values ($block_list);
arsort($block_list);

foreach ($block_list as $ip => $quantity) {
	echo "<li>$ip  - <strong>$quantity</strong></li>";
}


?>
</ul>
</body>
</html>
