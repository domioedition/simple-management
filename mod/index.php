<?php
if(!function_exists('checkAuth') && !function_exists('checkAccess')){
	header('Location: ../index.php');
	exit;
}else{
//	checkAuth();
//	checkAccess(14);
}

if($_SESSION['userAccess'] >= 14){
$sql = "SELECT `id` FROM reports";
$result = mysqli_query($link, $sql);
$countEvents = mysqli_num_rows($result);
mysqli_free_result($result);
$sql = "SELECT `id` FROM notes";
$result = mysqli_query($link, $sql);
$countNotes = mysqli_num_rows($result);
mysqli_free_result($result);

$sql = "SELECT `id` FROM clients";
$result = mysqli_query($link, $sql);
$cntClients = mysqli_num_rows($result);
mysqli_free_result($result);
?>
<br>
<br>
<div class="row">
    <div class="col-lg-3 col-md-6">
    <div class="panel panel-primary">
    <div class="panel-heading">
    <div class="row">
    <div class="col-xs-3">
    <i class="fa fa-comments fa-5x"></i>
    </div>
    <div class="col-xs-9 text-right">
    <div class="huge"><?=$countEvents?></div>
    <div>Reports</div>
    </div>
    </div>
    </div>
    <a href="../index.php?page=reports">
    <div class="panel-footer">
    <span class="pull-left">View Details</span>
    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
    <div class="clearfix"></div>
    </div>
    </a>
    </div>
    </div>
    
    <div class="col-lg-3 col-md-6">
    <div class="panel panel-green">
    <div class="panel-heading">
    <div class="row">
    <div class="col-xs-3">
    <i class="fa fa-tasks fa-5x"></i>
    </div>
    <div class="col-xs-9 text-right">
    <div class="huge"><?=$countNotes?></div>
    <div>MyNotes</div>
    </div>
    </div>
    </div>
    <a href="../index.php?page=notes">
    <div class="panel-footer">
    <span class="pull-left">View Details</span>
    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
    <div class="clearfix"></div>
    </div>
    </a>
    </div>
    </div>
	    
    <div class="col-lg-3 col-md-6">
    <div class="panel panel-primary">
    <div class="panel-heading">
    <div class="row">
    <div class="col-xs-3">
    <i class="fa fa-tasks fa-5x"></i>
    </div>
    <div class="col-xs-9 text-right">
    <div class="huge"><?=$cntClients?></div>
    <div>Clients</div>
    </div>
    </div>
    </div>
    <a href="../index.php?page=clients">
    <div class="panel-footer">
    <span class="pull-left">View Details</span>
    <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
    <div class="clearfix"></div>
    </div>
    </a>
    </div>
    </div>
</div>



<?
}
if($_SESSION['userAccess'] == 1){
    include('mod/modReportsSupport.php');
}
?>