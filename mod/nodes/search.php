<?php
// Передаем заголовки
header('Content-type: text/plain; charset=utf-8');
header('Cache-Control: no-store, no-cache');
header('Expires: ' . date('r'));


// Читаем POST параметр
if (!empty($_GET['searchWord'])){
	@$searchWord = $_GET['searchWord'];
	//echo searchInDB($searchWord);

}


	global $result;
	
	$conn = new mysqli('localhost', 'username', 'password', 'sm');
	mysqli_query($conn, "SET NAMES utf8");
	mysqli_query($conn, "SET CHARACTER SET 'utf8';"); 
	mysqli_query($conn, "SET SESSION collation_connection = 'utf8_general_ci';"); 	

	// Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	} 

	if (preg_match("/((25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(25[0-5]|2[0-4]\d|[01]?\d\d?)/", $searchWord)) {
		$sql = "SELECT * FROM nodes WHERE ip_address LIKE '%$searchWord%' ORDER BY LENGTH(ip_address),ip_address";
	} elseif(preg_match("/([0-9a-fA-F]{2}([:-]|$)){6}$|([0-9a-fA-F]{4}([.]|$)){3}/", $searchWord)) {
		$sql = "SELECT * FROM nodes WHERE mac_address='$searchWord'";
	} elseif(preg_match("/ /", $searchWord)) {
		$fullAddress = explode(" ", $searchWord);
		$sql = "SELECT * FROM nodes WHERE address LIKE '%$searchWord%'";
		// $sql = "SELECT * FROM nodes WHERE address LIKE '%$fullAddress[0]%' AND address LIKE '%$fullAddress[1]%' AND address LIKE '%$fullAddress[2]%'";
	} else {
		$sql = "SELECT * FROM nodes WHERE address LIKE '%$searchWord%' ORDER BY LENGTH(address),address";
	}
	// echo $sql;
	$r = $conn->query($sql);
	if ($r->num_rows > 0) {
	    // output data of each row
	    while($row = $r->fetch_assoc()) {
	    	//var_dump($row);
	        $ipAddress = $row['ip_address'];
	        $macAddress = $row['mac_address'];
	        $address = $row['address'];
	        $result .= $ipAddress." - ".$macAddress." - ".$address."\n";
	    }
	} else {
	    $result = 0;
	}
	$conn->close();
	echo $result;
?>