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
					<h3 class="panel-title">Online Exam Questions List</h3>
				</div>
			</div>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<?php
				$sql = 'SELECT online_exam_id,exam_subject,exam_title FROM exam_table WHERE admin_name=:current_user';
				$statement = db()->prepare($sql);
				$statement->bindValue(':current_user', current_user());
				$statement->execute();
				$rows = $statement->fetchAll(PDO::FETCH_ASSOC);
				foreach ($rows as $row) {
					echo "<b>Subject : " . $row['exam_subject'] . "</b><br>";
					echo "<b>Exam    : " . $row['exam_title'] . "</b><br>";
					echo "<br>";
					echo '<table id="exam_data_table" class="table table-bordered table-striped table-hover">';
					echo "<thead>";
					echo "<tr>";
					echo '<th>Question</th>';
					echo		'<th>Option A</th>';
					echo		'<th>Option B</th>';
					echo		'<th>Option C</th>';
					echo		'<th>Option D</th>';
					echo		'<th>Correct Answer</th>';
					echo	'</tr>';
					echo "</thead>";
					$sql = 'SELECT question_id,QUESTION_TITLE,ANSWER_OPTION FROM QUESTION_TABLE WHERE ONLINE_EXAM_ID=:id';
					$statement = db()->prepare($sql);
					$statement->bindValue(':id', $row['online_exam_id']);
					$statement->execute();
					$questions = $statement->fetchAll(PDO::FETCH_ASSOC);
					// print_r($questions);
					// echo count($questions);
					$sql = 'SELECT OPTION_NUMBER,OPTION_TITLE FROM OPTION_TABLE WHERE QUESTION_ID=:id';
					$statement = db()->prepare($sql);
					
					foreach($questions as $question){
						$statement->bindValue(':id', $question['question_id']);
						$statement->execute();
						$options = $statement->fetchAll(PDO::FETCH_ASSOC);
						echo "<tr>";
					echo "<td>". $question['QUESTION_TITLE'] ."</td>";
					echo "<td>". $options[0]['OPTION_TITLE'] ."</td>";
					echo "<td>". $options[1]['OPTION_TITLE'] ."</td>";
					echo "<td>". $options[2]['OPTION_TITLE'] ."</td>";
					echo "<td>". $options[3]['OPTION_TITLE'] ."</td>";
					echo "<td>". $question['ANSWER_OPTION'] ."</td>";
					echo "</tr>";
					}
					echo "</table>";
					echo "<br>";
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
	$('#viewQ').addClass('active');
</script>