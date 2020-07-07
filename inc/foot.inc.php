
</div>
<!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->

</div>
<!-- /#wrapper -->




<!-- jQuery -->
<script src="../bower_components/jquery/dist/jquery.min.js"></script>

<!-- Bootstrap Core JavaScript -->
<script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

<!-- Metis Menu Plugin JavaScript -->
<script src="../bower_components/metisMenu/dist/metisMenu.min.js"></script>

<!-- DataTables JavaScript -->
<script src="../bower_components/datatables/media/js/jquery.dataTables.min.js"></script>
<script src="../bower_components/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.min.js"></script>
<!-- <script src="../vendor/datatables-responsive/dataTables.responsive.js"></script> -->


<!-- Custom Theme JavaScript -->
<script src="../dist/js/sb-admin-2.js"></script>
	
<!-- Page-Level Demo Scripts - Tables - Use for reference -->
<script type="text/javascript">
$(document).ready(function() {
	$('#dataTables-example').DataTable({
			responsive: true,
			"aaSorting": [[0,'desc']],
			"iDisplayLength": 100,
			"aLengthMenu": [[10, 25, 50, 100,500,1000,-1], [10, 25, 50,100,500,1000, "All"]]
	});
	$('#tableDevice').DataTable({
			responsive: true,
			"aaSorting": [[0,'asc']],
			"iDisplayLength": 24,
			"aLengthMenu": [[8, 16, 24,-1], [8, 16, 24, "All"]]
	});
});
</script>

    <!-- Add mousewheel plugin (this is optional) -->
    <script type="text/javascript" src="../libs/fancybox/jquery.mousewheel-3.0.6.pack.js"></script>

    <!-- Add fancyBox main JS and CSS files -->
    <script type="text/javascript" src="../libs/fancybox/jquery.fancybox.js?v=2.1.5"></script>
    <link rel="stylesheet" type="text/css" href="../libs/fancybox/jquery.fancybox.css?v=2.1.5" media="screen" />

    <!-- Add Button helper (this is optional) -->
    <link rel="stylesheet" type="text/css" href="../libs/fancybox/jquery.fancybox-buttons.css?v=1.0.5" />
    <script type="text/javascript" src="../libs/fancybox/jquery.fancybox-buttons.js?v=1.0.5"></script>

    <!-- Add Thumbnail helper (this is optional) -->
    <link rel="stylesheet" type="text/css" href="../libs/fancybox/jquery.fancybox-thumbs.css?v=1.0.7" />
    <script type="text/javascript" src="../libs/fancybox/jquery.fancybox-thumbs.js?v=1.0.7"></script>

    <!-- Add Media helper (this is optional) -->
    <script type="text/javascript" src="../libs/fancybox/jquery.fancybox-media.js?v=1.0.6"></script>
    
<!--
    <link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css" rel="stylesheet">
	<script src="http://code.jquery.com/jquery-2.0.3.min.js"></script>
	<script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/js/bootstrap.min.js"></script>  
    <link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.4.6/bootstrap-editable/css/bootstrap-editable.css" rel="stylesheet"/>
    <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.4.6/bootstrap-editable/js/bootstrap-editable.min.js"></script>
	<script src="../mod/main.js"></script>
-->
	
	
<script type="text/javascript" src="../js/xmlhttprequest.js"></script>
<script type="text/javascript" src="../js/myscript.js"></script>	
	
</body>

</html>