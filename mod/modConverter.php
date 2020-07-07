<?php
$bytes = '';
if($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$bytes = $_POST['bytes'];
	$mbits = (($bytes/1024)/1024)*8;
}
?>




<div class="row">
<div class="col-lg-8">
<form role="form" action="" method="post">
	<div class="form-group">
	<input class="form-control" type="text" name="bytes" value="<?=$bytes?>" placeholder="bytes">
	</div>
	<input type="submit" value="Convert" name="do" class="btn btn-primary">				
</form>
</div>
</div>


<div class="row">
<div class="col-lg-8">
<h3><code><?php
if(!empty($mbits)){
	echo 'Result:';
	printf("%.2f Mbit/s\n", $mbits);
}
?></code></h3>
</div>
</div>