<?
checkAccess(14);
$report = new Reports();
// $sql = "SELECT
// 		reportsNew.id,
// 		reportsNew.date,
// 		reportsNew.user_id,
// 		reportsNew.category,
// 		reportsNew.content,
// 		users.id,
// 		users.username 
// 	FROM reportsNew, users WHERE reportsNew.user_id = users.id ORDER BY date DESC LIMIT 1000";

$sql = "SELECT * FROM reportsNew ORDER BY date DESC LIMIT 1000";
$reports = $report->getRows($sql);



?>


<div class="row">
	<div class="col-lg-12">
		<h3 class="page-header">12-10-2016</h3>
	</div>
</div>


<div class="row well well-sm">
<div class="col-lg-12">
<h4>Network</h4>
<div class="row">
	<div class="col-lg-2">
		<p class="text-danger">Dmitrij Ivanickij</p>
	</div>
	<div class="col-lg-4"><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Numquam tempore eum quaerat at quod, alias rem deserunt? Rem laboriosam aliquid doloribus eveniet mollitia soluta odio, quaerat hic reiciendis corporis ullam. Minima aliquid, nesciunt. Est, aperiam, quam! Facere, pariatur! Pariatur sed quis, facere quisquam aut necessitatibus veniam iste, sit officiis excepturi eos id ad animi explicabo doloribus natus ullam sint quasi cum.</p></div>
</div>
<div class="row">
	<div class="col-lg-2"><p class="text-danger">Mikle</p></div>
	<div class="col-lg-10"><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aperiam temporibus porro id, accusantium repudiandae perspiciatis, tempore officia, ipsam debitis sunt perferendis. Ex magni dignissimos ipsa, asperiores nemo omnis odit ducimus perspiciatis qui error, sit obcaecati eligendi nobis tempore mollitia autem eius repellendus expedita rem. Ipsum iure quibusdam accusamus doloribus delectus.</p></div>
</div>
<div class="row">
	<div class="col-lg-2"><p class="text-danger">garry</p></div>
	<div class="col-lg-10"><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Voluptatum perspiciatis distinctio, eaque totam aperiam ad ipsum quas iusto sapiente ducimus blanditiis deleniti corporis, dignissimos facilisunt. Qui commodi eum cum cupiditate cumque quis ratione! Dolorum inventore reiciendis quaerat necessitatibus temporibus.</p></div>
</div>

</div>
</div>

<div class="row well well-sm">
<div class="col-lg-12">
<h4>Equipment</h4>
<div class="row">
	<div class="col-lg-2">
		<p class="text-danger">Dmitrij Ivanickij</p>
	</div>
	<div class="col-lg-10 "><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Numquam tempore eum quaerat at quod, alias rem deserunt? Rem laboriosam aliquid doloribus eveniet mollitia soluta odio, quaerat hic reiciendis corporis ullam. Minima aliquid, nesciunt. Est, aperiam, quam! Facere, pariatur! Pariatur sed quis, facere quisquam aut necessitatibus veniam iste, sit officiis excepturi eos id ad animi explicabo doloribus natus ullam sint quasi cum.</p></div>
</div>
<div class="row">
	<div class="col-lg-2"><p class="text-danger">Mikle</p></div>
	<div class="col-lg-10"><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aperiam temporibus porro id, accusantium repudiandae perspiciatis, tempore officia, ipsam debitis sunt perferendis. Ex magni dignissimos ipsa, asperiores nemo omnis odit ducimus perspiciatis qui error, sit obcaecati eligendi nobis tempore mollitia autem eius repellendus expedita rem. Ipsum iure quibusdam accusamus doloribus delectus.</p></div>
</div>
<div class="row">
	<div class="col-lg-2"><p class="text-danger">garry</p></div>
	<div class="col-lg-10"><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Voluptatum perspiciatis distinctio, eaque totam aperiam ad ipsum quas iusto sapiente ducimus blanditiis deleniti corporis, dignissimos facilisunt. Qui commodi eum cum cupiditate cumque quis ratione! Dolorum inventore reiciendis quaerat necessitatibus temporibus.</p></div>
</div>

</div>
</div>

<div class="row well well-sm">
<div class="col-lg-6">
<h4>Change</h4>
<div class="row">
	<div class="col-lg-2">
		<p class="text-danger">Dmitrij Ivanickij</p>
	</div>
	<div class="col-lg-2"><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Numquam tempore eum quaerat at quod, alias rem deserunt? Rem laboriosam aliquid doloribus eveniet mollitia soluta odio, quaerat hic reiciendis corporis ullam. Minima aliquid, nesciunt. Est, aperiam, quam! Facere, pariatur! Pariatur sed quis, facere quisquam aut necessitatibus veniam iste, sit officiis excepturi eos id ad animi explicabo doloribus natus ullam sint quasi cum.</p></div>
</div>
<div class="row">
	<div class="col-lg-2"><p class="text-danger">Mikle</p></div>
	<div class="col-lg-2"><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aperiam temporibus porro id, accusantium repudiandae perspiciatis, tempore officia, ipsam debitis sunt perferendis. Ex magni dignissimos ipsa, asperiores nemo omnis odit ducimus perspiciatis qui error, sit obcaecati eligendi nobis tempore mollitia autem eius repellendus expedita rem. Ipsum iure quibusdam accusamus doloribus delectus.</p></div>
</div>
<div class="row">
	<div class="col-lg-2"><p class="text-danger">garry</p></div>
	<div class="col-lg-2"><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Voluptatum perspiciatis distinctio, eaque totam aperiam ad ipsum quas iusto sapiente ducimus blanditiis deleniti corporis, dignissimos facilisunt. Qui commodi eum cum cupiditate cumque quis ratione! Dolorum inventore reiciendis quaerat necessitatibus temporibus.</p></div>
</div>

</div>
</div>

<div class="row well well-sm">
<div class="col-lg-12">
<h4>Clients</h4>
<div class="row">
	<div class="col-lg-2">
		<p class="text-danger">Dmitrij Ivanickij</p>
	</div>
	<div class="col-lg-10 "><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Numquam tempore eum quaerat at quod, alias rem deserunt? Rem laboriosam aliquid doloribus eveniet mollitia soluta odio, quaerat hic reiciendis corporis ullam. Minima aliquid, nesciunt. Est, aperiam, quam! Facere, pariatur! Pariatur sed quis, facere quisquam aut necessitatibus veniam iste, sit officiis excepturi eos id ad animi explicabo doloribus natus ullam sint quasi cum.</p></div>
</div>
<div class="row">
	<div class="col-lg-2"><p class="text-danger">Mikle</p></div>
	<div class="col-lg-10"><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aperiam temporibus porro id, accusantium repudiandae perspiciatis, tempore officia, ipsam debitis sunt perferendis. Ex magni dignissimos ipsa, asperiores nemo omnis odit ducimus perspiciatis qui error, sit obcaecati eligendi nobis tempore mollitia autem eius repellendus expedita rem. Ipsum iure quibusdam accusamus doloribus delectus.</p></div>
</div>
<div class="row">
	<div class="col-lg-2"><p class="text-danger">garry</p></div>
	<div class="col-lg-10"><p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Voluptatum perspiciatis distinctio, eaque totam aperiam ad ipsum quas iusto sapiente ducimus blanditiis deleniti corporis, dignissimos facilisunt. Qui commodi eum cum cupiditate cumque quis ratione! Dolorum inventore reiciendis quaerat necessitatibus temporibus.</p></div>
</div>

</div>
</div>

			<div class="row">

				<div class="col-lg-6">
					<div class="well well-sm">
						<h4>Clients</h4>
						<p>На Завтра <br />
						drim_shev<br />
						Шевченка, 358<br />
						по білінгу<br />
						статична ІР <br />
						підключення 13.10<br />
						<br />
						<br />
						топологія 5.5 <br />
						 Masa16 - varshav -56-66 - Pancha 9 - Varchav 68</p>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-6">
					<div class="well well-sm">
						<h4>Change equipment</h4>
						<p></p>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="well well-sm">
						<h4>Other</h4>
						<p>На 6 карте окончательно удалён 6 аксес формата "блокировать все арп-пакеты".<br />
						<br />
						Около 17:00 правый кондиционер решил, что 38 градусов - нормальная температура, в результате по перегреву проебался биллинг. 17:20 - fixed.<br />
						<br />
						Коллеги! В 6 утра пропал свет. Серверная обесточена. в 7-10 электропитание восстановлено.</p>
					</div>
				</div>
			</div>

</div>



<hr>


<a href="/reports/?action=add"><button type="button" class="btn btn-success">Add new</button></a><br><br>
<div class="row">
<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">Reports</div>
        <!-- /.panel-heading -->
        <div class="panel-body">
            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Category</th>
                        <th>User</th>
                        <th>Report</th>
                        <th>Comments</th>
                    </tr>
                </thead>
                <tbody>
			<?
			if(is_array($reports)){
				foreach ($reports as $key => $value) {
					$id = $value['id'];
					// echo $id."<br>";
					// print_r($value);
					$date = date("Y-m-d H:i",$value['date']);
					// $username = $value['username'];
					$username = 0;
					// $sql = "SELECT COUNT(*) FROM reports_comment WHERE report_id='$id'";
					$sql = "SELECT * FROM reports_comment WHERE report_id='$id'";
					$r = $link->query($sql);
					$row_cnt = $r->num_rows;
					$category = $value['category'];
					switch ($category) {
						case '1':
							$category = "Network";
							// $class = "success";
							break;
						case '2':
							$category = "Clients";
							// $class = "info";
							break;
						case '3':
							$category = "Equipment";
							// $class = "warning";
							break;
						case '4':
							$category = "Other";
							// $class = "info";
							break;
						default:
							$category;
							$class = "";
							break;
					}
					$class = "";
					$content = nl2br($value['content']);
			echo "<tr class=\"$class\" onclick=\"window.location.href='/reports/?action=view&id=$id'\">
				<td width=\"130px\">$date</td>
				<td>$category</td>
				<td>$username</td>
				<td><p>$content</p></td>
				<td><center><code>$row_cnt</code></center></td>
			</tr>";
					}
				}
			?>
                </tbody>
            </table>
            <!-- /.table-responsive -->
		</div>
	</div>
</div>
</div>
