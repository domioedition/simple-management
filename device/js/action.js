//Save Config
function saveConfig(){
	var ip = document.getElementById("ip").value;
	var stringSend = "ip="+ip+"&action=saveConfig";
	request = getXmlHttpRequest();
	request.onreadystatechange = function(){
		if(request.readyState != 4){ return; }
		var div = document.getElementById("deviceInfo");
		div.innerHTML="";
		var result = request.responseText;
		div.innerHTML = result;			
	};
	request.open("POST", "action.php", true);
	request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	request.send(stringSend);

	function timer(){
	var ip = document.getElementById('ip').value;
	var obj=document.getElementById('timer_inp');
	obj.innerHTML--;
	if(obj.innerHTML==0){
		window.location = "http://95.69.244.151/device/?ip="+ip;
		setTimeout(function(){},1000);
	}
		else{setTimeout(timer,1000);}
	}
	setTimeout(timer,1000);
}

//Reconnect port
function portReconnect(){
	var ip = document.getElementById("ip").value;
	var port = document.getElementById("port").value;
	var stringSend = "ip="+ip+"&port="+port+"&action=reconnect";
	request = getXmlHttpRequest();
	request.onreadystatechange = function(){
		if(request.readyState != 4){ return; }
		var div = document.getElementById("portStatus").firstElementChild;
		div.innerHTML="";
		var result = request.responseText;
		// console.log(ip);
		div.innerHTML = result;			
	};
	request.open("POST", "action.php", true);
	request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	request.send(stringSend);	
}

function portEnable(){
	var ip = document.getElementById("ip").value;
	var port = document.getElementById("port").value;
	var stringSend = "ip="+ip+"&port="+port+"&action=enable";
	request = getXmlHttpRequest();
	request.onreadystatechange = function(){
		if(request.readyState != 4){ return; }
		var div = document.getElementById("portStatus").firstElementChild;
		div.innerHTML="";
		var result = request.responseText;
		// console.log(ip);
		div.innerHTML = result;			
	};
	request.open("POST", "action.php", true);
	request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	request.send(stringSend);	
}


function portDisable(){
	var ip = document.getElementById("ip").value;
	var port = document.getElementById("port").value;
	var stringSend = "ip="+ip+"&port="+port+"&action=disable";
	request = getXmlHttpRequest();
	request.onreadystatechange = function(){
		if(request.readyState != 4){ return; }
		var div = document.getElementById("portStatus").firstElementChild;
		div.innerHTML="";
		var result = request.responseText;
		// console.log(ip);
		div.innerHTML = result;			
	};
	request.open("POST", "action.php", true);
	request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	request.send(stringSend);	
}

function portAuto(){
	var ip = document.getElementById("ip").value;
	var port = document.getElementById("port").value;
	var stringSend = "ip="+ip+"&port="+port+"&action=auto";
	request = getXmlHttpRequest();
	request.onreadystatechange = function(){
		if(request.readyState != 4){ return; }
		var div = document.getElementById("portStatus").firstElementChild;
		div.innerHTML="";
		var result = request.responseText;
		// console.log(ip);
		div.innerHTML = result;			
	};
	request.open("POST", "action.php", true);
	request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	request.send(stringSend);	
}

function port100(){
	var ip = document.getElementById("ip").value;
	var port = document.getElementById("port").value;
	var stringSend = "ip="+ip+"&port="+port+"&action=100";
	request = getXmlHttpRequest();
	request.onreadystatechange = function(){
		if(request.readyState != 4){ return; }
		var div = document.getElementById("portStatus").firstElementChild;
		div.innerHTML="";
		var result = request.responseText;
		// console.log(ip);
		div.innerHTML = result;			
	};
	request.open("POST", "action.php", true);
	request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	request.send(stringSend);	
}

function port10(){
	var ip = document.getElementById("ip").value;
	var port = document.getElementById("port").value;
	var stringSend = "ip="+ip+"&port="+port+"&action=10";
	request = getXmlHttpRequest();
	request.onreadystatechange = function(){
		if(request.readyState != 4){ return; }
		var div = document.getElementById("portStatus").firstElementChild;
		div.innerHTML="";
		var result = request.responseText;
		div.innerHTML = result;			
	};
	request.open("POST", "action.php", true);
	request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	request.send(stringSend);	
}


////Other actions with port
//showErrors
function showPortErrors(){
	// console.log("error");
	var ip = document.getElementById("ip").value;
	var port = document.getElementById("port").value;
	var stringSend = "ip="+ip+"&port="+port+"&action=showPortError";
	request = getXmlHttpRequest();
	request.onreadystatechange = function(){
		if(request.readyState != 4){ return; }
		var div = document.getElementById("portInfo");
		div.innerHTML="";
		var result = request.responseText;
		div.innerHTML = result;			
	};
	request.open("POST", "action.php", true);
	request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	request.send(stringSend);
}

//showCableDiag
function showCableDiag(){
	var ip = document.getElementById("ip").value;
	var port = document.getElementById("port").value;
	var stringSend = "ip="+ip+"&port="+port+"&action=showCableDiag";
	request = getXmlHttpRequest();
	request.onreadystatechange = function(){
		if(request.readyState != 4){ return; }
		var div = document.getElementById("portInfo");
		div.innerHTML="";
		var result = request.responseText;
		div.innerHTML = result;			
	};
	request.open("POST", "action.php", true);
	request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	request.send(stringSend);
}

//showMacAddress
function showMacAddress(){
	var ip = document.getElementById("ip").value;
	var port = document.getElementById("port").value;
	var stringSend = "ip="+ip+"&port="+port+"&action=showMacAddress";
	request = getXmlHttpRequest();
	request.onreadystatechange = function(){
		if(request.readyState != 4){ return; }
		var div = document.getElementById("portInfo");
		div.innerHTML="";
		var result = request.responseText;
		div.innerHTML = result;			
	};
	request.open("POST", "action.php", true);
	request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	request.send(stringSend);
}