<?

//Вывод всех портов


?>


<div class="row">
<div class="col-lg-12">
<div class="panel panel-default">
    <div class="panel-heading">
        Table ports
    </div>
    <!-- /.panel-heading -->
    <div class="panel-body">
        <table width="100%" class="table table-striped table-bordered table-hover" id="tableDevice">
            <thead>
                <tr>
                    <th>Port</th>
                    <th>State</th>
                    <th>Settings/Speed/Duplex</th>
                    <th>Connection</th>
                </tr>
            </thead>
			<?
				if($ip){
					echo $device->getPorts($ip);
				}
			?>
        </table>
        <!-- /.table-responsive -->
    </div>
    <!-- /.panel-body -->
</div>
<!-- /.panel -->
</div>
<!-- /.col-lg-12 -->
</div>
<!-- /.row -->