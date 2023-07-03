<?php
require __DIR__ . '/../src/bootstrap.php';
require_login();
error_reporting(E_ALL);
ini_set('display_errors', '1');
?>
<?php view('sidebar') ?>
<div class="right-panel">
<div class="card">
	<div class="card-header">
		<div class="row">
			<div class="col-md-9">
				<h3 class="panel-title">Online Exam List</h3>
			</div>
		</div>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table id="exam_data_table" class="table table-bordered table-striped table-hover">
				<thead>
					<tr>
						<th>Exam Subject</th>
						<th>Exam Title</th>
						<th>Date & Time</th>
						<th>Duration</th>
						<th>Total Question</th>
						<th>Status</th>
					</tr>
				</thead>
				<?php
$sql = 'SELECT * FROM exam_table WHERE admin_name=:current_user';
$statement = db()->prepare($sql);
$statement->bindValue(':current_user', current_user());
$statement->execute();
$rows = $statement->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $row) {
    echo '<tr>';
    echo '<td>' . $row['exam_subject'] . '</td>';
    echo '<td>' . $row['exam_title'] . '</td>';
    echo '<td>' . $row['exam_datetime'] . '</td>';
    echo '<td>' . $row['exam_duration'] . '</td>';
    echo '<td>' . $row['total_question'] . '</td>';
	if($row['online_exam_status']=='Questions Pending') {
    echo '<td style="background-color:#F9AEAE">' . $row['online_exam_status'] . '</td>';
}
else if($row['online_exam_status']=='Enrollment Pending'){
	echo '<td style="background-color:#ffb6c1">' . $row['online_exam_status'] . '</td>';
	}
	else if($row['online_exam_status']=='Created')
    echo '<td style="background-color:#C4A484">' . $row['online_exam_status'] . '</td>';
	else if($row['online_exam_status']=='Started')
    echo '<td style="background-color:#ADD8E6">' . $row['online_exam_status'] . '</td>';
	else if($row['online_exam_status']=='Completed')
    echo '<td style="background-color:#90ee90">' . $row['online_exam_status'] . '</td>';
    echo '</tr>';
}
?>
			</table>
		</div>
	</div>
</div>
</div>

<?php view('footer') ?>

<script>     


   // Remove any existing active classes
   $('#dashboard').removeClass('active');
   $('#vix').addClass('active');
</script>
