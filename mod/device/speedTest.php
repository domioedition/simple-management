<?php
$ip = $_GET['ip'];
$port = $_GET['port'];
?>
<html>
<head>
<link href="../../inc/my.css" rel="stylesheet">
<script type="text/javascript" src="../../js/xmlhttprequest.js"></script>
<script type="text/javascript">

	//получаем данные от скрипта о загрузке порта
	function getUtilization(ip,port){
		request = getXmlHttpRequest();
		request.onreadystatechange = function(){
			if(request.readyState != 4){ return; }
			//console.log(request.responseText);
			var result = request.responseText;
			result = result.split(":");
			//1 вариант вывода.
			//var div = document.getElementById("utilization");
			//div.firstChild.nodeValue = "Download: "+result[0]+" Mbit/s";
			//div.nextSibling.nodeValue = "Upload: "+result[1]+" Mbit/s";
			//второй вариант вывода.
			document.getElementById("RX").innerHTML="Download: "+result[1]+" Mbit/s";
			document.getElementById("TX").innerHTML="Upload: "+result[0]+" Mbit/s";
			//console.log(ip+" : "+ port);
		};
		request.open("GET", "showUtilization.php?"+"ip="+ip+"&port="+port, true);
		request.send(null);			
	}
	window.onload = function(){
		var ip = document.getElementById("ip").value;
		var port = document.getElementById("port").value;
		var timerId = setInterval(function() {	getUtilization(ip,port);}, 1500);
		setTimeout(function() {
			clearInterval(timerId);
			alert("Прошло 10 минут.");
			}, 600000);
		//600000 миллисекунды.
	}
</script>
</head>


<body>
<h3>Speed Test</h3>
<p>IP-address: <?=$ip?></p>
<p>Port: <?=$port?></p>
<input type="text" id="ip" value="<?=$ip?>" hidden>
<input type="text" id="port" value="<?=$port?>" hidden>
<div id="utilization">
	<p id="RX"></p>
	<p id="TX"></p>
</div>
<hr>
<center>
	<button onclick="location.reload()">Refresh</button>
	<button onclick="window.close()">Close</button>
</center>
</body>


</html>