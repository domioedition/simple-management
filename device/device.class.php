<?
/**
* мой класс Device
*/
class Device
{
	public $isConn;
	protected $db;
	public function __construct($username = "username", $password="password", $servername = "localhost", $dbName = "sm") {
		$this->isConn = true;
		try {
			$this->db = new mysqli($servername, $username, $password, $dbName);
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}
	
	public function Disconnect(){
		$this->db = null;
		$this->isConn = false;
		echo "Disconnected\n";
	}

	//Функция запроса на получение модели свича
	public function getModel($ip, $comm='public'){
			// global $ip, $model;
			$model = @snmpget($ip, $comm, "1.3.6.1.2.1.1.1.0", '10000','2');
			if(is_string($model))
			{
				if(stripos($model, 'DES-3526') == true) $model='DES-3526';
				if(stripos($model, 'DES-3200') == true) $model='DES-3200';
				if(stripos($model, 'DES-3528') == true) $model='DES-3528';
				return $model;
			}else{
				header('Location: ../error.php?code=2&ip='.$ip);
				die("ERROR");
			}
	}

	//Функция на сохранение конфигурации
	public function saveConfig($ip, $comm='public'){
		$model = $this->getModel($ip);
		switch($model){
			case 'DES-3526': @snmpset($ip, $comm, "1.3.6.1.4.1.171.12.1.2.6.0", "i", "3", timeout, 0); break;
			case 'DES-3200': @snmpset($ip, $comm, "1.3.6.1.4.1.171.12.1.2.6.0", "i", "5", timeout, 0); break;
			case 'DES-3528': @snmpset($ip, $comm, "1.3.6.1.4.1.171.12.1.2.6.0", "i", "5", timeout, 0); break;
		}
		echo "<h2>Save configuration</h2><h4>Configuration and log saved to flash.</h4><p>You'll be automatically redirected to control device after <code id=\"timer_inp\">45</code> seconds.</p>";
		$this->logSave($ip, "0", "Save");
	}

	//Функция логирования
	public function logSave($ip, $port, $action){
		$user_id = $_SESSION['userId'];
		$date = time();
		$query = "INSERT INTO logs_ports_action (user_id, date, ip_address, port, action) VALUES ('$user_id', '$date', '$ip', '$port', '$action')";

		try {
			if ($this->db->query($query) === TRUE) {
				return true;
			} else {
				// echo "Error: " . $sql . "<br>" . $conn->error;
				return false;
			}
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}		
	}

	//Запрос sysName
	public function getSysName($ip, $comm='public'){
		$sysName = snmpwalkoid($ip, $comm, "1.3.6.1.2.1.1.5");
		$sysName = str_replace("STRING: ","",implode(",", $sysName));
		return $sysName;
	}
	
	//Запрос sysLocation (карта)
	public function getSysLocation($ip, $comm='public'){
		$sysLocation = snmpwalkoid($ip, $comm, "1.3.6.1.2.1.1.6");
		$sysLocation = str_replace("STRING: ","",implode(",", $sysLocation));
		return $sysLocation;
	}

	//Запрос sysContact  (кольцо)
	public function getSysContact($ip, $comm='public'){
		$sysContact = snmpwalkoid($ip, $comm, "1.3.6.1.2.1.1.4");
		$sysContact = str_replace("STRING: ","",implode(",", $sysContact));
		return $sysContact;
	}

	//получаем таблицу всех портов
	public function getPorts($ip, $comm='public'){
		$model = $this->getModel($ip);
		switch($model){
			case 'DES-3526':
				//Админ. статус порта
				$portAdminState = snmpwalk($ip, $comm, "1.3.6.1.4.1.171.11.64.1.2.4.2.1.3");
				//Выставленная скорость на порту
				$portCtrlNwayState = snmpwalk($ip, $comm, "1.3.6.1.4.1.171.11.64.1.2.4.2.1.4");
				//Скорость линка на которой порт поднялся
				$portNwayStatus = snmpwalk($ip, $comm, "1.3.6.1.4.1.171.11.64.1.2.4.1.1.5");
				break;
			case 'DES-3200':
				$portAdminState = snmpwalk($ip, $comm, "1.3.6.1.4.1.171.11.113.1.5.2.2.2.1.3");
				$portCtrlNwayState = snmpwalk($ip, $comm, "1.3.6.1.4.1.171.11.113.1.5.2.2.2.1.4");
				$portNwayStatus = snmpwalk($ip, $comm, "1.3.6.1.4.1.171.11.113.1.5.2.2.1.1.5");
				break;
			case 'DES-3528':
				$portAdminState = snmpwalk($ip, $comm, "1.3.6.1.4.1.171.11.105.1.2.3.2.1.4");
				$portCtrlNwayState = snmpwalk($ip, $comm, "1.3.6.1.4.1.171.11.105.1.2.3.2.1.5");
				$portNwayStatus = snmpwalk($ip, $comm, "1.3.6.1.4.1.171.11.105.1.2.3.1.1.6");
				break;
		}
		//Обрезаем кол-во портов до 24 штук	
		array_splice($portAdminState, 24);
		array_splice($portCtrlNwayState, 24);
		array_splice($portNwayStatus, 24);
		//Фомируем струкртуру таблицы
		$tbody = "<tbody>";
		for ($i = 1; $i < 25; $i++){
			$portState = substr($portAdminState[$i-1],8,9);
			$pSpeed = substr($portNwayStatus[$i-1],8,9);
			$pSpeedState = substr($portCtrlNwayState[$i-1],8,9);
			#Админ. статус порта
			switch($portState){
				case 2: $portState = 'Disable';	break;
				case 3: $portState = 'Enable'; break;
			}
			#Выставленная скорость на порту
			if($model == 'DES-3526'){
				switch($pSpeedState){
					case 2: $pSpeedState = 'Auto'; break;
					case 3: $pSpeedState = '10Mbps-Half'; break;
					case 4: $pSpeedState = '10Mbps-Full'; break;
					case 5: $pSpeedState = '100Mbps-Half'; break;
					case 6: $pSpeedState = '100Mbps-Full'; break;
					case 7: $pSpeedState = '1Gigabps-Half'; break;
					case 8: $pSpeedState = '1Gigabps-Full'; break;
				}
			}
			if($model == 'DES-3200'){
				switch($pSpeedState){
					case 1: $pSpeedState = 'Auto'; break;
					case 2: $pSpeedState = '10Mbps-Half'; break;
					case 3: $pSpeedState = '10Mbps-Full'; break;
					case 4: $pSpeedState = '100Mbps-Half'; break;
					case 5: $pSpeedState = '100Mbps-Full'; break;
					case 6: $pSpeedState = '1Gigabps-Half'; break;
					case 7: $pSpeedState = '1Gigabps-Full'; break;
				}
			}
			if($model == 'DES-3528'){
				switch($pSpeedState){
					case 2: $pSpeedState = 'Auto'; break;
					case 3: $pSpeedState = '10Mbps-Half'; break;
					case 4: $pSpeedState = '10Mbps-Full'; break;
					case 5: $pSpeedState = '100Mbps-Half'; break;
					case 6: $pSpeedState = '100Mbps-Full'; break;
					case 7: $pSpeedState = '1Gigabps-Half'; break;
					case 8: $pSpeedState = '1Gigabps-Full'; break;
				}
			}
			#Скорость линка на которой порт поднялся
			if($model == 'DES-3526'){
				switch($pSpeed){
					case 2: $pSpeed = '<button type="button" class="btn btn-default btn-xs">Link Down</button>'; break;
					case 3: $pSpeed = '<button type="button" class="btn btn-warning btn-xs">10Mbps-Half</button>';	break;
					case 4: $pSpeed = '<button type="button" class="btn btn-warning btn-xs">10Mbps-Full</button>'; break;
					case 5: $pSpeed = '<button type="button" class="btn btn-warning btn-xs">100Mbps-Half</button>'; break;	
					case 6: $pSpeed = '<button type="button" class="btn btn-success btn-xs">100Mbps-Full</button>'; break;
				}
			}
			if($model == 'DES-3200'){
				switch($pSpeed){
					case 1: $pSpeed = '<button type="button" class="btn btn-default btn-xs">Link Down</button>'; break;
					case 2: $pSpeed = '<button type="button" class="btn btn-warning btn-xs">10Mbps-Half</button>'; break;
					case 3: $pSpeed = '<button type="button" class="btn btn-warning btn-xs">10Mbps-Full</button>'; break;
					case 4: $pSpeed = '<button type="button" class="btn btn-warning btn-xs">100Mbps-Half</button>'; break;
					case 5: $pSpeed = '<button type="button" class="btn btn-success btn-xs">100Mbps-Full</button>'; break;
				}
			}
			if($model == 'DES-3528'){
				switch($pSpeed){
					case 0: $pSpeed = '<button type="button" class="btn btn-default btn-xs">Link Down</button>'; break;
					case 4: $pSpeed = '<button type="button" class="btn btn-warning btn-xs">10Mbps-Half</button>';	break;
					case 2: $pSpeed = '<button type="button" class="btn btn-warning btn-xs">10Mbps-Full</button>'; break;
					case 8: $pSpeed = '<button type="button" class="btn btn-warning btn-xs">100Mbps-Half</button>'; break;
					case 6: $pSpeed = '<button type="button" class="btn btn-success btn-xs">100Mbps-Full</button>'; break;
				}
			}
			$tbody .= "<tr onclick=\"window.location.href='?ip=$ip&port=$i'\">
				<td><button type=\"button\" class=\"btn btn-default btn-xs disabled\">$i</button></td>
				<td>$portState</td>
				<td>$pSpeedState</td>
				<td>$pSpeed</td>
			</tr>";
			}
			$tbody .= '</tbody>';
			return $tbody;
	}

	//Запрос bandwidth_control
	public function getPortBandwidth($ip, $port, $comm='public')
	{
		$model = $this->getModel($ip);
		switch($model){
			case 'DES-3526':
				$bandRx = snmpwalk($ip, $comm, "1.3.6.1.4.1.171.11.64.1.2.6.1.1.2");
				$bandTx = snmpwalk($ip, $comm, "1.3.6.1.4.1.171.11.64.1.2.6.1.1.3");
				$bandRx = substr($bandRx[$port-1],8,9);
				$bandTx = substr($bandTx[$port-1],8,9);
				if($bandRx == 0) $bandRx='no_limit';
				if($bandTx == 0) $bandTx='no_limit';
				break;

			case 'DES-3200':
				$bandRx = snmpwalk($ip, $comm, "1.3.6.1.4.1.171.11.113.1.5.2.3.1.1.2");
				$bandTx = snmpwalk($ip, $comm, "1.3.6.1.4.1.171.11.113.1.5.2.3.1.1.3");
				$bandRx = substr($bandRx[$port-1],8,9)*0.0009765625;
				$bandTx = substr($bandTx[$port-1],8,9)*0.0009765625;
				if($bandRx == 1024000) $bandRx='no_limit';
				if($bandTx == 1024000) $bandTx='no_limit';
				break;

			case 'DES-3528':
				$bandRx = snmpwalk($ip, $comm, "1.3.6.1.4.1.171.12.61.3.1.1.2");
				$bandTx = snmpwalk($ip, $comm, "1.3.6.1.4.1.171.12.61.3.1.1.3");
				$bandRx = substr($bandRx[$port-1],8,9)*0.0009765625;
				$bandTx = substr($bandTx[$port-1],8,9)*0.0009765625;
				if($bandRx == 0) $bandRx='no_limit';
				if($bandTx == 0) $bandTx='no_limit';
				break;
		}
		$bandwidth = "<h4>Bandwidth control</h4>";
		$bandwidth .= "<p>RX Rate: <code>$bandRx</code></p>";
		$bandwidth .= "<p>TX Rate: <code>$bandTx</code></p>";
		echo $bandwidth;
	}

	// Запрос port description	
	public function getPortDescription($ip, $port, $comm='public'){
		$portDescription = snmpwalk($ip, $comm, "1.3.6.1.2.1.31.1.1.1.18.".$port);
		$portDescription = str_replace("STRING: ","", $portDescription[0]);
		$pos = strripos($portDescription, "IP:");
		if($pos === false){
			$client_ip = "No ip address in description.";
		}else{
			$client_ip = explode(":", $portDescription);
			$client_ip = str_replace("CL", "", $client_ip['1']);
		}
		$pos = strripos($portDescription, "CL:");
		if($pos === false){
			$client_id = "";
			$client_id = "Unknown ClientID.";
		}else{
			$client_id = explode(":", $portDescription);
			$client_id = "<a target=_blank href=https://billing.airbites.net.ua/clients/?View=SEE&Name=$client_id[2]>$client_id[2]</a>";
		}
		$clientInfo = "<h4>Client</h4>";
		$clientInfo .= "<p>Client IP: <code>$client_ip</code></p>";
		$clientInfo .= "<p>Client ID: <code>$client_id</code></p>";
		echo $clientInfo;
	}

	// Переподключение порта
	public function portReconnect($ip, $port, $comm='public'){
		successMsg("Port is reconnected.");
		$model = $this->getModel($ip);
		//проверяем или порт входит в массив.
		checkPort($port);
		//Чистим арп таблицу.
		$clear_arp = snmpset($ip, $comm, "1.3.6.1.4.1.171.12.1.2.12.1.0","i",2);
		//Админ. статус порта
		switch($model){
			case 'DES-3526': $portAdminState = snmpwalk($ip, $comm, "1.3.6.1.4.1.171.11.64.1.2.4.2.1.3.$port"); break;
			case 'DES-3200': $portAdminState = snmpwalk($ip, $comm, "1.3.6.1.4.1.171.11.113.1.5.2.2.2.1.3.$port"); break;
			case 'DES-3528': $portAdminState = snmpwalk($ip, $comm, "1.3.6.1.4.1.171.11.105.1.2.3.2.1.4.$port.1"); break;
		}
		$portAdminState = substr($portAdminState[0],8,9);
		//если статус порта включен, то отсылаем mib на переподключение
		if($portAdminState != '2') {
			//Выключаем порт!!!
			switch($model){
				case 'DES-3526':snmpset($ip, $comm,"1.3.6.1.2.1.2.2.1.7.".$port,'i',2); break;
				case 'DES-3200':snmpset($ip, $comm,"1.3.6.1.4.1.171.11.113.1.5.2.2.2.1.3.".$port.".100",'i',2); break;
				case 'DES-3528':snmpset($ip, $comm,"1.3.6.1.4.1.171.11.105.1.2.3.2.1.4.".$port.".1",'i',2); break;
			}
			//Устанавливаем задержку 2 секунды;
			sleep(3);
			//Включем порт!!!		
			switch($model){
				case 'DES-3526':snmpset($ip, $comm,"1.3.6.1.2.1.2.2.1.7.".$port,'i',1); break;
				case 'DES-3200':snmpset($ip, $comm,"1.3.6.1.4.1.171.11.113.1.5.2.2.2.1.3.".$port.".100",'i',3); break;
				case 'DES-3528':snmpset($ip, $comm,"1.3.6.1.4.1.171.11.105.1.2.3.2.1.4.".$port.".1",'i',3); break;
			}
			//Чистим арп таблицу.
			$clear_arp = snmpset($ip, $comm, "1.3.6.1.4.1.171.12.1.2.12.1.0","i",2);
			
			//Запись логов в БД
			$this->logSave($ip, $port, "Reconnected");
		}
	}

	// Включение порта
	public function portEnable($ip, $port, $comm='public')
	{
		$model = $this->getModel($ip);
		if($model == 'DES-3526') $r = snmpset($ip, $comm, "1.3.6.1.2.1.2.2.1.7.".$port,'i',1);
		if($model == 'DES-3200') $r = snmpset($ip, $comm, "1.3.6.1.4.1.171.11.113.1.5.2.2.2.1.3.".$port.".100",'i',3);
		if($model == 'DES-3528') $r = snmpset($ip, $comm, "1.3.6.1.4.1.171.11.105.1.2.3.2.1.4.".$port.".1",'i',3);
		$this->logSave($ip, $port, "Enabled");
		successMsg("Port is <code>enabled.</code>");
	}

	// Выключение порта
	public function portDisable($ip, $port, $comm='public')
	{
		$model = $this->getModel($ip);
		if($model == 'DES-3526') snmpset($ip, $comm, "1.3.6.1.2.1.2.2.1.7.".$port,'i',2);
		if($model == 'DES-3200') snmpset($ip, $comm, "1.3.6.1.4.1.171.11.113.1.5.2.2.2.1.3.".$port.".100",'i',2);
		if($model == 'DES-3528') snmpset($ip, $comm, "1.3.6.1.4.1.171.11.105.1.2.3.2.1.4.".$port.".1",'i',2);
		$this->logSave($ip, $port, "Disabled");
		successMsg("Port is disabled.");
	}

	// Скорость порта Auto
	public function portAuto($ip, $port, $comm='public')
	{
		$model = $this->getModel($ip);
		if($model == 'DES-3526') snmpset($ip, $comm, "1.3.6.1.4.1.171.11.64.1.2.4.2.1.4.".$port, 'i', 2);
		if($model == 'DES-3200') snmpset($ip, $comm, "1.3.6.1.4.1.171.11.113.1.5.2.2.2.1.4.".$port.".100",'i',1);
		if($model == 'DES-3528') snmpset($ip, $comm, "1.3.6.1.4.1.171.11.105.1.2.3.2.1.5.".$port.".1",'i',2);
		$this->logSave($ip, $port, "speed Auto");
		successMsg("Port speed is Auto.");
	}
	// Скорость порта 100
	public function port100($ip, $port, $comm='public')
	{
		$model = $this->getModel($ip);
		if($model == 'DES-3526') snmpset($ip, $comm, "1.3.6.1.4.1.171.11.64.1.2.4.2.1.4.".$port, 'i', 6);
		if($model == 'DES-3200') snmpset($ip, $comm, "1.3.6.1.4.1.171.11.113.1.5.2.2.2.1.4.".$port.".100",'i',5);
		if($model == 'DES-3528') snmpset($ip, $comm, "1.3.6.1.4.1.171.11.105.1.2.3.2.1.5.".$port.".1",'i',6);		
		$this->logSave($ip, $port, "speed 100_full");
		successMsg("Port speed is 100_full.");
	}
	
	// Скорость порта 10
	public function port10($ip, $port, $comm='public')
	{
		$model = $this->getModel($ip);
		if($model == 'DES-3526') snmpset($ip, $comm, "1.3.6.1.4.1.171.11.64.1.2.4.2.1.4.".$port, 'i', 4);
		if($model == 'DES-3200') snmpset($ip, $comm, "1.3.6.1.4.1.171.11.113.1.5.2.2.2.1.4.".$port.".100",'i',3);
		if($model == 'DES-3528') snmpset($ip, $comm, "1.3.6.1.4.1.171.11.105.1.2.3.2.1.5.".$port.".1",'i',4);	
		$this->logSave($ip, $port, "speed 10_full");
		successMsg("Port speed is 10_full.");
	}

	// Показать ошибки на порту
	public function showErrors($ip, $port, $comm='public'){
		// $model = $this->getModel($ip);
		// $this->logSave($ip, $port, "showErrors");

	@$portCRCErrors = snmpwalk($ip, $comm, "1.3.6.1.2.1.16.1.1.1.8.".$port);
	@$portUndersize = snmpwalk($ip, $comm, "1.3.6.1.2.1.16.1.1.1.9.".$port);
	@$portOversize = snmpwalk($ip, $comm, "1.3.6.1.2.1.16.1.1.1.10.".$port);
	@$portFragment = snmpwalk($ip, $comm, "1.3.6.1.2.1.16.1.1.1.11.".$port);
	@$portJabber = snmpwalk($ip, $comm, "1.3.6.1.2.1.16.1.1.1.12.".$port);
	@$portDropPkts = snmpwalk($ip, $comm, "1.3.6.1.2.1.16.1.1.1.3.".$port);
	@$portCollision = snmpwalk($ip, $comm, "1.3.6.1.2.1.16.1.1.1.13.".$port);

	$portCRCErrors = strRem($portCRCErrors[0]);
	$portUndersize = strRem($portUndersize[0]);
	$portOversize = strRem($portOversize[0]);
	$portFragment = strRem($portFragment[0]);
	$portJabber = strRem($portJabber[0]);
	$portDropPkts = strRem($portDropPkts[0]);

	$portCollision = strRem($portCollision[0]);
	//Формируем таблицу результатов.
	$errors =  "
		<h4>Errors</h4>
		<div class=\"table-responsive\">
		<table class=\"table\">
			<tbody>
				<tr>
					<td>CRC Error</td>
					<td><code>$portCRCErrors</td>
				</tr>
				<tr>
					<td>Undersize</td>
					<td><code>$portUndersize</code></td>
				</tr>
				<tr>
					<td>Oversize</td>
					<td><code>$portOversize</code></td>
				</tr>
				<tr>
					<td>Fragment</td>
					<td><code>$portFragment</code></td>
				</tr>
				<tr>
					<td>Jabber</td>
					<td><code>$portJabber</code></td>
				</tr>
				<tr>
					<td>Drop Pkts</td>
					<td><code>$portDropPkts</code></td>
				</tr>
					<tr>
					<td>Collision</td>
					<td><code>$portCollision</code></td>
				</tr>
			</tbody>
		</table>
		</div>";
		echo $errors;
	}


	public function showCableDiag($ip, $port, $comm='public'){
		$model = $this->getModel($ip);
		if($model == 'DES-3526'){
		//Старт диагностики
		// Запускаем тест
		$startDiagnostic = snmpset($ip, $comm, "1.3.6.1.4.1.171.12.58.1.1.1.12.".$port, "i", 1);
		// Проверяем или он завершился
		if($startDiagnostic != 2){
			die("Error. Start diagnostic status = ".$startDiagnostic);
		}
		// Проверка активности линка(Result: 1-Link Up, 0 - Link down)
		$linkStatus =	 snmpwalk($ip, $comm, "1.3.6.1.4.1.171.12.58.1.1.1.3.".$port);
		//Состояние пар
		$linkPair1 =	 snmpwalk($ip, $comm, "1.3.6.1.4.1.171.12.58.1.1.1.4.".$port);
		$linkPair2 =	 snmpwalk($ip, $comm, "1.3.6.1.4.1.171.12.58.1.1.1.5.".$port);
		//$linkPair3 =	 snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.12.58.1.1.1.6.".$port);
		//$linkPair4 =	 snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.12.58.1.1.1.7.".$port);
		//Длинна кабеля 1-ая и 2-ая пары
		$linkLenghtPair1 = snmpwalk($ip, $comm, "1.3.6.1.4.1.171.12.58.1.1.1.8.".$port);
		$linkLenghtPair2 = snmpwalk($ip, $comm, "1.3.6.1.4.1.171.12.58.1.1.1.9.".$port);
		//$linkLenghtPair3 = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.12.58.1.1.1.10".$port);
		//$linkLenghtPair4 = snmpwalk($ip, rcomm, "1.3.6.1.4.1.171.12.58.1.1.1.11".$port);
		$linkStatus = implode(",", $linkStatus);
		$linkStatus = substr($linkStatus,8,9);

		$linkPair1 = implode(",", $linkPair1);
		$linkPair1 = substr($linkPair1,8,9);

		$linkPair2 = implode(",", $linkPair2);
		$linkPair2 = substr($linkPair2,8,9);

		$linkLenghtPair1 = implode(",", $linkLenghtPair1);
		$linkLenghtPair1 = substr($linkLenghtPair1,8,9);

		$linkLenghtPair2 = implode(",", $linkLenghtPair2);
		$linkLenghtPair2 = substr($linkLenghtPair2,8,9);

		switch($linkPair1){
			case '0' : $linkPair1 = 'OK'; break;
			case '1' : $linkPair1 = 'open'; break;
			case '2' : $linkPair1 = 'short'; break;
			case '3' : $linkPair1 = 'open-short'; break;
			case '4' : $linkPair1 = 'crosstalk'; break;
			case '5' : $linkPair1 = 'unknown'; break;
			case '6' : $linkPair1 = 'count'; break;
			case '7' : $linkPair1 = 'no-cable'; break;
			case '8' : $linkPair1 = 'Other'; break;
		}

		switch($linkPair2){
			case '0' : $linkPair2 = 'OK'; break;
			case '1' : $linkPair2 = 'open'; break;
			case '2' : $linkPair2 = 'short'; break;
			case '3' : $linkPair2 = 'open-short'; break;
			case '4' : $linkPair2 = 'crosstalk'; break;
			case '5' : $linkPair2 = 'unknown'; break;
			case '6' : $linkPair2 = 'count'; break;
			case '7' : $linkPair2 = 'no-cable'; break;
			case '8' : $linkPair2 = 'Other'; break;
		}
 		//Вывод информации о статусе линка Up Down		
		if($linkStatus == 1) $linkStatus = 'Link Up'; else $linkStatus = 'Link Down';
		//Результаты теста
			if($linkPair1 == 'OK' && $linkPair2 == 'OK'){
				$testResult = 'OK';
			}elseif($linkPair1 == 'no-cable' && $linkPair2 == 'no-cable'){
				$testResult = 'No Cable';
			}elseif($linkPair1 == 'short'){
				$testResult = "Pair1 " . $linkPair1 . " at " . $linkLenghtPair1 . " M<br>";
			}elseif($linkPair2 == 'short'){
				$testResult = "Pair2 " . $linkPair2 . " at " . $linkLenghtPair2 . " M<br>";
			}else{
				$testResult = "Pair1 " . $linkPair1 . " at " . $linkLenghtPair1 . " M<br>" . "Pair2 " . $linkPair2 . " at " . $linkLenghtPair2 . " M";
			}
		//Вывод длинны кабеля
		if ($linkLenghtPair1 == $linkLenghtPair2 && $linkLenghtPair1 != -1 ){
			$linkLenght = '<code>'.$linkLenghtPair1.'</code>';
		}else{
			$linkLenght = '-';
		}
		//Если длиина первой пары равна -1, то это значит что в порте нет кабеля
		if($linkLenghtPair1 == -1){
			$linkLenght = 'Couldn\'t show length.';
		}
		$cableDiag = "<h4>Perform Cable Diagnostics ...</h4>
					<div class=\"table-responsive\">
					<table class=\"table\">
						<thead>
						<tr>
							<th>Link Status</th>
							<th>Test Result</th>
							<th>Cable Length (M)</th>
						</tr>
						</thead>
						<tbody>
							<tr>
								<td>$linkStatus</td>
								<td>$testResult</td>
								<td>$linkLenght</td>
							</tr>
						</tbody>
					</table>";
		} else {
			$cableDiag = "<h1>Only DES-3526 models.</h1>";
		}
		echo $cableDiag;
	}

	public function showMacAddress($ip, $port, $comm='public')
	{
		function macDecHex($p){
			global $result;
				$result = '';
				$m = explode(".",$p);
				foreach($m as $part){
					$x = dechex($part);
					if(strlen($x)<2) $x = '0'.$x;
					$result .= $x.':';			
				}
				$result = substr($result,0,-1);
				return $result;
		}
		//временная переменная r  - result
		$r = '';
		$vlanTag = snmpwalkoid($ip, rcomm, "1.3.6.1.2.1.17.7.1.4.5.1.1.".$port);
		$vlanTag = implode(",",$vlanTag);
		$vlanTag = str_replace('Gauge32: ', '', $vlanTag);
		$vlanName = snmpwalk($ip, rcomm, "1.3.6.1.2.1.17.7.1.4.3.1.1.".$vlanTag);
		$vlanName = implode(",",$vlanName);
		$vlanName = str_replace("\"","",str_replace('STRING: ', '', $vlanName));
		$macArr = snmpwalkoid($ip, rcomm, "1.3.6.1.2.1.17.7.1.2.2.1.2.".$vlanTag);
		// print_r($macArr);
		$tmpArr = array();
		if(is_array($macArr)){
			foreach($macArr as $key=>$mac){
				$searchMac = 'INTEGER: '.$port;
				if($mac == $searchMac){
					$mac = str_replace('SNMPv2-SMI::mib-2.17.7.1.2.2.1.2.'.$vlanTag.'.','',$key);
					// echo $mac."<br>";
					$tmpArr[] = $mac;
				}			
			}
			// echo count($tmpArr);
			if(count($tmpArr)>0){
				foreach($tmpArr as $item){
					$r .= $vlanTag."^".$vlanName."^".macDecHex($item)."^".$port."|";				
				}
			}else{
				$r .= $vlanTag."^".$vlanName."^"."no mac address"."^".$port."|";
			}
		}else{
			$r .= $vlanTag."^".$vlanName."^"."no mac address"."^".$port."|";
		}
		$r = substr($r,0,-1);
		// return $r;

		$result = explode("|", $r);

		// var_dump($result);
		$tr = "\n\n<tr>";
		$td = "";
		foreach ($result as $key => $row) {
			$item = explode("^", $row);
			foreach ($item as $key => $value) {
				$td .= "<td>$value</td>";
			}
			$tr .= $td."</tr>\n\n<tr>";
		}
		$tr .= "</tr>";

		// return $r;

		// if($macAddress == '00:'){$macAddress = 'no mac address';}else{$macAddress = substr($macAddress,0,strlen($macAddress)-1);}
		$macAddress = "
		<div class=\"table-responsive\">
		<table class=\"table\">
			<thead>
				<tr>
					<th>VID</th>
					<th>VLAN Name</th>
					<th>mac-address</th>
					<th>port</th>
				</tr>
			</thead>
			<tbody>$tr
			</tbody>
		</table>
		</div>";
		echo $macAddress;		
	}
}
?>
