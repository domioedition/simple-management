

$(function(){
	var $links = $('.lldp_menu li a');
	$links.on('click', function(e){
		var $siblings = $(this).siblings('ul');
		e.preventDefault();
		$siblings.slideToggle();
		console.log($siblings);
	});
});



	//Check port state, speed.
	function checkPortStatus(){
		var ip = document.getElementById("ip").value;
		var port = document.getElementById("port").value;
		var stringSend = "ip="+ip+"&port="+port;
		request = getXmlHttpRequest();
		request.onreadystatechange = function(){
			if(request.readyState != 4){ return; }
			var div = document.getElementById("portStatus").firstElementChild;
			div.innerHTML="";
			var result = request.responseText;
			// console.log(ip);
			div.innerHTML = result;			
		};
		request.open("POST", "../device/showPort.php", true);
		request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		request.send(stringSend);	
	}
	//Check 10 access
	function check10Access(){
		var ip = document.getElementById("ip").value;
		var port = document.getElementById("port").value;
		var stringSend = "ip="+ip+"&port="+port;
		request = getXmlHttpRequest();
		request.onreadystatechange = function(){
			if(request.readyState != 4){ return; }
			var div = document.getElementById("result").firstElementChild;
			div.innerHTML="";
			var result = request.responseText;
			div.innerHTML = result;			
		};
		request.open("POST", "../mod/device/check10Access.php", true);
		request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		request.send(stringSend);	
	}
	//Get utilization
	function getUtilization(){
		var ip = document.getElementById("ip").value;
		var port = document.getElementById("port").value;
		var stringSend = "ip="+ip+"&port="+port;
		request = getXmlHttpRequest();
		request.onreadystatechange = function(){
			if(request.readyState != 4){ return; }
			var div = document.getElementById("result").firstElementChild;
			div.innerHTML="";
			var result = request.responseText;
			result = result.split(":");
			var p = document.createElement('p');
			p.innerHTML = "Download: <code>"+result[1]+"</code> Mbit/s";
			div.appendChild(p);
			var p = document.createElement('p');
			p.innerHTML = "Upload: <code>"+result[0]+"</code> Mbit/s";
			div.appendChild(p);
		};
		request.open("POST", "../mod/device/showUtilization.php", true);
		request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		request.send(stringSend);		
	}


	//Show utilization
	function showUtilization(){
		var ip = document.getElementById("ip").value;
		var port = document.getElementById("port").value;
		var newWin = window.open("../mod/device/speedTest.php?"+"ip="+ip+"&port="+port,"", 'width=860, height=470, top='+((screen.height-470)/2)+',left='+((screen.width-860)/2)+', resizable=no, scrollbars=no, status=yes, toolbar=no, location=no');
		newWin.focus();		// var timerId = setInterval(function() {	getUtilization(ip,port);}, 1500);
		// setTimeout(function() {
		// 	clearInterval(timerId);
		// 	alert("Прошло 10 минут.");
		// 	}, 600000);	//600000 миллисекунды.
	}
	
	//функция отображения мак адресса
	function showMac(){
		var ip = document.getElementById("ip").value;
		var port = document.getElementById("port").value;
		var stringSend = "ip="+ip+"&port="+port;
		request = getXmlHttpRequest();
		request.onreadystatechange = function(){
			if(request.readyState != 4){ return; }
			var div = document.getElementById("result").firstElementChild;
			div.innerHTML="";
			var result = request.responseText;
			div.innerHTML = result;			
		};
		request.open("POST", "../mod/device/showMac.php", true);
		request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		request.send(stringSend);
	}
	

	//Функиция диагностики кабеля.
	function cableDiag(){
		var ip = document.getElementById("ip").value;
		var port = document.getElementById("port").value;
		var stringSend = "ip="+ip+"&port="+port;
		request = getXmlHttpRequest();
		request.onreadystatechange = function(){
			if(request.readyState != 4){ return; }
			var div = document.getElementById("result").firstElementChild;
			div.innerHTML="";
			var result = request.responseText;
			div.innerHTML = result;			
		};
		request.open("POST", "../mod/device/cableDiag.php", true);
		request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		request.send(stringSend);
	}


	//Функиция показа ошибок
	function showErrors(){
		var ip = document.getElementById("ip").value;
		var port = document.getElementById("port").value;
		var stringSend = "ip="+ip+"&port="+port;
		request = getXmlHttpRequest();
		request.onreadystatechange = function(){
			if(request.readyState != 4){ return; }
			var div = document.getElementById("result").firstElementChild;
			div.innerHTML="";
			var result = request.responseText;
			div.innerHTML = result;			
		};
		request.open("POST", "../mod/device/errors.php", true);
		request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		request.send(stringSend);
	}
	window.onload = function(){
	}

/*	var input = document.getElementById("searchReport");
	input.oninput = function() {
		if(input.value.length < 4){
			var div = document.getElementById("result");
			div.innerHTML = "";
			// var divContent = document.getElementById("contentReports");
			// divContent.innerHTML = "";
		}
		if(input.value.length > 4){
			var searchWord = input.value;
			//Отправляем запрос
			var req = getXmlHttpRequest();
			req.open('GET', "../mod/search.php?"+"searchWord="+searchWord, true);
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
	};*/

//поиск в репортс
function searchInReports(){
	console.log(123);
}

	//fancybox
	$(".fancybox").fancybox();