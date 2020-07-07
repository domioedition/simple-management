<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

	
    <title>SM::Simple Management</title>
	
	<link rel="shortcut icon" href="/img/favicon.ico" /> 
	
	<!-- Bootstrap Core CSS -->
    <link href="../bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="../bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="../bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" href="../inc/common.css" type="text/css">
<script type="text/javascript">


</script>

   

</head>

<body>

<div id="wrapper">

<!-- Navigation -->
<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
<div class="navbar-header">
<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
<span class="sr-only">Toggle navigation</span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
<span class="icon-bar"></span>
</button>
<a class="navbar-brand" href="../">SM::Simple Management</a>
</div>
<!-- /.navbar-header -->

<ul class="nav navbar-top-links navbar-right">
<li class="dropdown">
<a class="dropdown-toggle" data-toggle="dropdown" href="#">
<i class="fa fa-user fa-fw"></i> <?=$login;?> <i class="fa fa-caret-down"></i>
</a>
<ul class="dropdown-menu dropdown-user">
<li><a href="../index.php?page=user&action=edit&id=<?=$userId?>"><i class="fa fa-user fa-fw"></i> User Profile</a>
</li>
<li><a href="#"><i class="fa fa-gear fa-fw"></i> Settings</a>
</li>
<li class="divider"></li>
<li><a href="../login.php?logout"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
</li>
</ul>
<!-- /.dropdown-user -->
</li>
<!-- /.dropdown -->
</ul>
<!-- /.navbar-top-links -->

<div class="navbar-default sidebar" role="navigation">
<div class="sidebar-nav navbar-collapse">
<ul class="nav" id="side-menu">
<li class="sidebar-search">
<form role="form" action="../index.php" method="get">
<div class="input-group custom-search-form">
<input type="text" name="ip" value="<?=$ip?>" class="form-control" placeholder="ip-address">
<span class="input-group-btn">
<button class="btn btn-default" type="submit" title="Click to search">
<i class="fa fa-search"></i>
</button>
</span>
</div>
</form>
</li>

<?
if ($_SESSION['userAccess'] >= 14) {
?>
	<li><a href="../index.php?page=reports">Reports</a></li>
	<li><a href="../index.php?page=rSupport">Reports Support</a></li>
	<li><a href="../index.php?page=clients">Clients</a></li>
	<li><a href="../index.php?page=nodes">Nodes</a></li>
	<li><a href="../index.php?page=TopologyAll">Network Topology STP</a></li>
	<li><a href="../index.php?page=lldp">Network Topology LLDP</a></li>
	<li><a href="../index.php?page=getModels">Models</a></li>
	<li><a href="../index.php?page=converter">Converter</a></li>
	<li><a href="../index.php?page=searchPort">Search port</a></li>
	<li><a href="../index.php?page=checkoid">Check OID</a></li>
	<li><a href="../index.php?page=accessCreator">Access Creator</a></li>
	<li><a href="../index.php?page=notes">MyNotes</a></li>
	<li><a href="../index.php?page=user">Users</a></li>
	<li><a href="../index.php?page=logs">Logs</a></li>
    <li><a href="../test/">Test</a></li>
</ul>

<?
} elseif ($_SESSION['userAccess'] >= 10) {
    echo '<li><a href="index.php?page=rSupport">Reports Support</a></li>
    <li><a href="../index.php?page=manuals">Manuals</a></li>
	<li><a href="../index.php?page=clients">Clients</a></li>
    ';	
}
 else {
    echo '<li><a href="index.php?page=rSupport">Reports Support</a></li>
    <li><a href="../index.php?page=manuals">Manuals</a></li>';
}?>
</div>					
</div>

</nav>
<div id="page-wrapper">
<div class="container-fluid">

