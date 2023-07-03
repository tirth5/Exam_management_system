<?php

require __DIR__ . '/../src/bootstrap.php';
require_login();
?>

<?php view('sidebaruser') ?>
<div class="right-panel">
<div class="card">
		<div class="card-header">
			<div class="row">
				<div class="col-md-9">
					<h3 class="panel-title">Result List</h3>
				</div>
			</div>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<?php
                $sql='SELECT * from exam_table join enroll_table on exam_table.online_exam_id=enroll_table.exam_id WHERE enroll_table.user_id=:id ORDER BY exam_table.exam_datetime'; 
                $statement = db()->prepare($sql);
                $statement->bindValue(':id', current_user());
                $statement->execute();
                $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
                // var_dump($rows);
				foreach ($rows as $row) {
					echo '<table id="exam_data_table" class="table table-bordered table-striped table-hover">';
					echo "<thead>";
					echo "<tr>";
					echo '<th>Exam Subject</th>';
					echo  '<th>Exam Title</th>';
					echo '<th>Exam Datetime</th>';
					echo '<th>Total Marks</th>';
					echo '<th>Obtained Marks</th>';
					echo	'</tr>';
					echo "</thead>";

					$sql = 'SELECT user_id, SUM(CAST(marks AS SIGNED)) AS total_marks 
                    FROM student_exam 
                    WHERE exam_id = :id 
                    and user_id = :user_id';
					$statement = db()->prepare($sql);
					$statement->bindValue(':id', $row['online_exam_id']);
					$statement->bindValue(':user_id', current_user());
					$statement->execute();
					$details = $statement->fetchAll(PDO::FETCH_ASSOC);
                    
                    
					foreach ($details as $detail) {
						echo "<tr>";
						echo "<td>" . $row['exam_subject'] . "</td>";
						echo "<td>" . $row['exam_title'] . "</td>";
						echo "<td>" . $row['exam_datetime'] . "</td>";
						echo "<td>" . $row['total_question']*$row['marks_per_right_answer'] . "</td>";
						echo "<td>" . $detail['total_marks'] . "</td>";
						echo "</tr>";
					}
					echo "</table>";
					echo "<br>";
                    echo "<hr>";
				}
				?>

			</div>
		</div>
	</div>
</div>

<?php view('footer') ?>
<script>
       // Remove any existing active classes
   $('#dashboard').removeClass('active');
   $('#res').addClass('active');

</script>