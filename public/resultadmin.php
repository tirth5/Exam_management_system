<?php
require __DIR__ . '/../src/bootstrap.php';
require_login();
error_reporting(E_ALL);
ini_set('display_errors', '1');
?>
<?php view('sidebar') ?>
<?php
$current_datetime = date("Y-m-d") . ' ' . date("H:i:s", STRTOTIME(date('h:i:sa')));
?>
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
				$sql = 'SELECT online_exam_id,exam_subject,exam_title,exam_datetime FROM exam_table WHERE admin_name=:current_user order by exam_datetime DESC';
				$statement = db()->prepare($sql);
				$statement->bindValue(':current_user', current_user());
				$statement->execute();
				$rows = $statement->fetchAll(PDO::FETCH_ASSOC);
				foreach ($rows as $row) {
					echo "<div class='row'>";
					echo "<div class='col-md-9'>";
					echo "<b>Subject : " . $row['exam_subject'] . "</b><br>";
					echo "<b>Exam    : " . $row['exam_title'] . "</b><br>";
					echo "<b>Exam    : " . $row['exam_datetime'] . "</b><br>";
					echo "<br>";
					echo "</div>";
					echo "</div>";

					echo '<table id="exam_data_table" class="table table-bordered table-striped table-hover">';
					echo "<thead>";
					echo "<tr>";
					echo '<th>Roll No.</th>';
					echo		'<th>Unattempted</th>';
					echo		'<th>Marks</th>';
					echo	'</tr>';
					echo "</thead>";

					$sql = 'SELECT user_id, SUM(CAST(marks AS SIGNED)) AS total_marks 
                    FROM student_exam 
                    WHERE exam_id = :id 
                    GROUP BY user_id';
					$statement = db()->prepare($sql);
					$statement->bindValue(':id', $row['online_exam_id']);
					$statement->execute();
					$details = $statement->fetchAll(PDO::FETCH_ASSOC);
					foreach ($details as $detail) {
						$sql = 'SELECT COUNT(*) AS not_attempted
					FROM student_exam
					WHERE exam_id = :id AND user_id = :user_id AND user_answer_option = "E"';
						$statement = db()->prepare($sql);
						$statement->bindValue(':id', $row['online_exam_id']);
						$statement->bindValue(':user_id', $detail['user_id']);
						$statement->execute();
						$unattempted = $statement->fetchAll(PDO::FETCH_ASSOC);
						echo "<tr>";
						echo "<td>" . $detail['user_id'] . "</td>";
						echo "<td>" . $unattempted[0]["not_attempted"] . "</td>";
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
</div>

<?php view('footer') ?>

<script>
	// Remove any existing active classes
	$('#dashboard').removeClass('active');
	// Add active class to clicked link
	$('#resad').addClass('active');
</script>