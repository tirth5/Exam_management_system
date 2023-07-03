<?php
require __DIR__ . '/../src/bootstrap.php';
require_login();
error_reporting(E_ALL);
ini_set('display_errors', '1');
?>

<?php view('sidebaruser') ?>
<div class="right-panel">
<div class="card">
	<div class="card-header">
		<div class="row">
			<div class="col-md-9">
				<h3 class="panel-title">Exam List</h3>
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
						<th>Exam Status</th>
						<th>Action</th>
					</tr>
				</thead>
    	<?php 
        $sql='SELECT * from exam_table join enroll_table on exam_table.online_exam_id=enroll_table.exam_id WHERE enroll_table.user_id=:id ORDER BY exam_table.exam_datetime'; 
        $statement = db()->prepare($sql);
        $statement->bindValue(':id', current_user());
        $statement->execute();
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            // print_r($row);
            echo '<tr>';
            echo '<td>' . $row['exam_subject'] . '</td>';
            echo '<td>' . $row['exam_title'] . '</td>';
            echo '<td>' . $row['exam_datetime'] . '</td>';
            echo '<td>' . $row['exam_duration'] . ' Minute'.'</td>';
            $current_time = round(microtime(true) * 1000);
            $specified_time = strtotime($row['exam_datetime']) * 1000;

            if ($current_time >= $specified_time && $row['online_exam_status'] != 'Completed') {
                $sql='UPDATE EXAM_TABLE SET online_EXAM_STATUS=:status WHERE online_exam_id=:id';
                $statement = db()->prepare($sql);
                $statement->bindValue(':id', $row['online_exam_id']);
                $statement->bindValue(':status','Started');
                $statement->execute();
            }
            if($row['online_exam_status']=='Enrollment Pending'|| $row['online_exam_status']=='Questions Pending' || $row['online_exam_status']=='Created'){
        echo '<td style="background-color:#ffccbb">' .'Scheduled' . '</td>';
        echo '<td>' . 'Not Started' . '</td>';
            }
            else if($row['online_exam_status']=='Started'){
            echo '<td style="background-color:lightgreen">' . $row['online_exam_status'] . '</td>';
            echo '<td><a href="viewspecificexam.php?online_exam_id='.$row["online_exam_id"].'" class="btn btn-info btn-sm">Start Exam</a></td>';
            }
            else if($row['online_exam_status']=='Completed'){
                echo '<td style="background-color:grey;color:white;">' . $row['online_exam_status'] . '</td>';
                echo '<td><a href="review.php?online_exam_id='.$row["online_exam_id"].'" class="btn btn-info btn-sm">Review</a></td>';
            }
           
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
   $('#upcoming').addClass('active');


</script>