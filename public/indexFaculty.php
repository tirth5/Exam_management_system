<?php

require __DIR__ . '/../src/bootstrap.php';
require_login();
?>

<?php view('sidebar') ?>
<div class="right-panel">

	<?php
	$sql = "SELECT DISTINCT exam_subject FROM exam_table WHERE admin_name = :admin_name";
	$statement = db()->prepare($sql);
	$statement->bindValue(':admin_name', current_user());
	$statement->execute();
	$rows = $statement->fetchAll(PDO::FETCH_ASSOC);
	?>
	<div class="card-header">
		<div class="row">
			<div class="col-md-9">
				<h3 class="panel-title">Subjects</h3>
			</div>
		</div>
	</div>
	<?php
	echo '<div class="row">';
	// echo "Subjects";
	foreach ($rows as $row) {
		echo '<div class="col-4 my-3">';
		echo '<div class="card">';
		echo '<div class="card-body">';
		echo '<h5 class="card-title"> ' . $row['exam_subject'] . '</h5>';
		echo '</div>';
		echo '</div>';
		echo '</div>';
	}
	echo '</div>';
	?>
	<div class="card">
		<div class="card-header">
			<div class="row">
				<div class="col-md-9">
					<h3 class="panel-title">Exam Analysis</h3>
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
							<th>Roll No.(H)</th>
							<th>Highest</th>
							<th>Average</th>
						</tr>
					</thead>
					<?php
					$sql = 'SELECT * FROM exam_table WHERE admin_name=:current_user ORDER BY EXAM_DATETIME DESC';
					$statement = db()->prepare($sql);
					$statement->bindValue(':current_user', current_user());
					$statement->execute();
					$rows1 = $statement->fetchAll(PDO::FETCH_ASSOC);

					foreach ($rows1 as $row) {
						$sql2 = 'SELECT user_id, SUM(CAST(marks AS SIGNED)) AS total_marks 
	FROM student_exam 
	WHERE exam_id = :id 
	GROUP BY user_id 
	ORDER BY total_marks DESC 
	LIMIT 1';
						$statement2 = db()->prepare($sql2);
						$statement2->bindValue(':id', $row['online_exam_id']);
						$statement2->execute();
						$rows2 = $statement2->fetchAll(PDO::FETCH_ASSOC);
						echo "<br>";
						echo '<tr>';
						echo '<td>' . $row['exam_subject'] . '</td>';
						echo '<td>' . $row['exam_title'] . '</td>';
						echo '<td>' . $row['exam_datetime'] . '</td>';
						if (!empty($rows2) && array_key_exists('user_id', $rows2[0])) {
							echo '<td>' . $rows2[0]['user_id'] . '</td>';
						} else {
							echo '<td>N/A</td>';
						}
						if (!empty($rows2) && array_key_exists('total_marks', $rows2[0])) {
							echo '<td>' . $rows2[0]['total_marks'] . '</td>';
						} else {
							echo '<td>N/A</td>';
						}

						$sql3='SELECT AVG(total_marks) as average_marks 
						FROM (
						  SELECT user_id, SUM(CAST(marks AS SIGNED)) AS total_marks 
						  FROM student_exam 
						  WHERE exam_id = :id 
						  GROUP BY user_id
						) as subquery
						';

						$statement3 = db()->prepare($sql3);
						$statement3->bindValue(':id', $row['online_exam_id']);
						$statement3->execute();
						$result = $statement3->fetch(PDO::FETCH_ASSOC);
						$avg_marks = $result['average_marks'];
						if (!empty($avg_marks)) {
							echo '<td>' . $avg_marks . '</td>';
						} else {
							echo '<td>N/A</td>';
						}


						echo '</tr>';
					}

					?>
				</table>
			</div>
		</div>
	</div>
</div>
</div>
</div>
</div>


</div>
<?php view('footer') ?>
	