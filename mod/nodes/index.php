<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Nodes</title>
	<link rel="stylesheet" type="text/css" href="../../inc/my.css">
	<script type="text/javascript" src="../../js/xmlhttprequest.js"></script>
</head>
<body>
	<h1>Nodes</h1>
	<form>
	<input type="text" id="nodeInput">
	<button type="reset">Clear</button>
	</form>
	<script type="text/javascript">
	// var reset = document.getElementById("reset");
	// reset.addEventListener('click', function(){
	// 	input.innerHTML = '';
	// 	// return;
	// });
	var input = document.getElementById("nodeInput");
	input.oninput = function() {
		if(input.value.length < 4){
			var div = document.getElementById("result");
			div.innerHTML = "";
		}
		if(input.value.length > 4){
			var searchWord = input.value;
			//Отправляем запрос
			var req = getXmlHttpRequest();
			req.open('GET', "search.php?"+"searchWord="+searchWord, true);
			// req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			req.send(null);
			req.onreadystatechange = function(){
				if (req.readyState != 4) return;
				var responseText = String(req.responseText);
				var nodes = responseText.split("\n");
				var divResult = document.getElementById('result');
				divResult.innerHTML = "";
				for(var i=0; i<nodes.length; i++){
					if(nodes[i] == "") continue;
					if(nodes[i] == 0){
						divResult.innerHTML = "0 results";
					}
					var p = document.createElement('p');
					var textNode = document.createTextNode(nodes[i]);
					p.appendChild(textNode);
					// console.log(p);
					divResult.appendChild(p)
				}
			}
		}
	};
</script>
	<h3>Резульаты поиска:</h3>
	<pre><div id="result"></div></pre>
</body>
</html>