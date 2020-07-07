<input type="text" id="ip" value="<?=$ip?>" hidden>
<input type="text" id="port" value="<?=$port?>" hidden>

<script type="text/javascript">
	var timerId = setTimeout(function check() {
		checkPortStatus();
		// console.log('Checking port status...');
		timerId = setTimeout(check, 2000);
	}, 2000);
	clearTimeout(6000000); //таймаут 10 минут.
</script>
<div class="row">
	<div class="col-lg-6" id="portStatus">
		<div class="well well-sm"><h4>Please wait, loading...</h4></div>
		<div class="well well-sm">
			<button type="button" class="btn btn-outline btn-default btn-sm" onclick="portReconnect()">Reconnect</button>
			<button type="button" class="btn btn-outline btn-default btn-sm" onclick="portEnable()">Enable</button>
			<button type="button" class="btn btn-outline btn-default btn-sm" onclick="portDisable()">Disable</button>
			<button type="button" class="btn btn-outline btn-default btn-sm" onclick="portAuto()">Auto</button>
			<button type="button" class="btn btn-outline btn-default btn-sm" onclick="port100()">100_full</button>
			<button type="button" class="btn btn-outline btn-default btn-sm" onclick="port10()">10_full</button>
		<br>
		<br>
			<button type="button" class="btn btn-outline btn-default btn-sm" onclick="showPortErrors()">Errors</button>
			<button type="button" class="btn btn-outline btn-default btn-sm" onclick="showCableDiag()">CableDiag</button>
			<button type="button" class="btn btn-outline btn-default btn-sm" onclick="showMacAddress()">Mac-address</button>
			<button type="button" class="btn btn-outline btn-default btn-sm" onclick="Utilization()">Utilization</button>
			<button type="button" class="btn btn-outline btn-default btn-sm" onclick="showPortSecurity()">PortSecurity</button>
		</div>
	</div>
	<div class="col-lg-6">
		<div class="well well-sm">
		<?
			// getModel($ip);
			$device->getPortDescription($ip, $port);
		?>
		<?
			$device->getPortBandwidth($ip, $port);
		?>
		</div>
	</div>
</div>

<div class="row" id="portInfo">
	<div class="col-lg-12">
	</div>
</div>

