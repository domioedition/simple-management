
<?
checkAccess(14);
?>



<div class="row">
<div class="col-lg-12">
	<div class="panel panel-info">
	<div class="panel-heading">Добавление новой записи</div>
	<div class="panel-body">
		<div class="row">
			<div class="col-lg-6">

			<form method="post" action="add.php" role="form">
			<div class="form-group">
				<label>Категория</label>
				<select name="category" class="form-control">
					<option value="1">Network</option>
					<option value="2">Clients</option>
					<option value="3">Equipment</option>
					<option value="4">Other</option>
				</select>
			</div>
				<div class="form-group">
					<label>Запись</label>
					<textarea class="form-control" name="content" rows="10"></textarea><br>
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-primary">Добавить</button>
				</div>
			</form>

			</div>
		</div>
	</div>
	</div>
</div>
</div>