<?
session_start();
include('../../inc/conf.php');
include('../../inc/functions.php');


function getErrors($ip, $port, $rcomm="public"){
	@$portCRCErrors = snmpwalk($ip, $rcomm, "1.3.6.1.2.1.16.1.1.1.8.".$port);
	@$portUndersize = snmpwalk($ip, $rcomm, "1.3.6.1.2.1.16.1.1.1.9.".$port);
	@$portOversize = snmpwalk($ip, $rcomm, "1.3.6.1.2.1.16.1.1.1.10.".$port);
	@$portFragment = snmpwalk($ip, $rcomm, "1.3.6.1.2.1.16.1.1.1.11.".$port);
	@$portJabber = snmpwalk($ip, $rcomm, "1.3.6.1.2.1.16.1.1.1.12.".$port);
	@$portDropPkts = snmpwalk($ip, $rcomm, "1.3.6.1.2.1.16.1.1.1.3.".$port);
	@$portCollision = snmpwalk($ip, $rcomm, "1.3.6.1.2.1.16.1.1.1.13.".$port);

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
	// $errors = '<p>'.$portCRCErrors.'</p>:'.$portUndersize.':'.$portOversize.':'.$portFragment.':'.$portJabber.':'.$portDropPkts.':'.$portCollision;
	return $errors;
}

if($_SERVER['REQUEST_METHOD'] == "POST"){
	$ip = $_POST['ip'];
	$port = $_POST['port'];
	getModel();
	checkRmon($ip, "public", $model);
	echo getErrors($ip, $port);
}else{
	Error(1);
}
?>