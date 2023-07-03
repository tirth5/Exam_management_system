<?php
require __DIR__ . '/../src/bootstrap.php';
require_login();
error_reporting(E_ALL);
ini_set('display_errors', '1');
?>
<?php view('sidebar') ?>
<?php 
$current_datetime = date("Y-m-d") . ' ' . date("H:i:s", STRTOTIME(date('h:i:sa')));

if (is_post_request()) {
    $sql = 'INSERT INTO exam_table(admin_name,exam_title,exam_datetime,exam_duration, total_question, marks_per_right_answer, marks_per_wrong_answer, online_exam_created_on, online_exam_status,exam_subject,ENROLLMENT,QUESTIONS) VALUES (:admin_name,:exam_title,:exam_datetime,:exam_duration, :total_question, :marks_per_right_answer, :marks_per_wrong_answer, :online_exam_created_on, :online_exam_status, :subject1,:enrollment,:QUESTIONS)';

	echo "adsf";
    $statement = db()->prepare($sql);

    $statement->bindValue(':admin_name', current_user());
    $statement->bindValue(':exam_title',$_POST['online_exam_title']);
    $statement->bindValue(':exam_datetime',$_POST['online_exam_datetime']);
    $statement->bindValue(':exam_duration',$_POST['online_exam_duration']);
    $statement->bindValue(':total_question',$_POST['total_question']);
    $statement->bindValue(':marks_per_right_answer',$_POST['marks_per_right_answer']);
    $statement->bindValue(':marks_per_wrong_answer',$_POST['marks_per_wrong_answer']);
    $statement->bindValue(':online_exam_created_on',$current_datetime);
    $statement->bindValue(':online_exam_status','Questions Pending');
    $statement->bindValue(':subject1',$_POST['online_exam_subject']);
    $statement->bindValue(':enrollment','0');
    $statement->bindValue(':QUESTIONS','0');
    
    $statement->execute();
	$flashId = 'flash_' . uniqid();
    flash($flashId,"Exam Details Added successfully", FLASH_SUCCESS);

	$output = array(
		'success'	=>	'New Exam Details Added'
	);

	echo json_encode($output);
    }
?>
<div class="scheduleexamform right-panel">
	<div class="facultyform">
		<form id="exam_form">
			<div class="form-group">
				<div class="row">
					<label class="col-md-4 text-right">Exam Subject <span class="text-danger">*</span></label>
					<div class="col-md-8">
						<input type="text" name="online_exam_subject" id="online_exam_subject" class="form-control" />
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="row">
					<label class="col-md-4 text-right">Exam Title <span class="text-danger">*</span></label>
					<div class="col-md-8">
						<input type="text" name="online_exam_title" id="online_exam_title" class="form-control" />
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="row">
					<label class="col-md-4 text-right">Exam Date & Time <span class="text-danger">*</span></label>
					<div class="col-md-8">
						<input type="text" name="online_exam_datetime" id="online_exam_datetime" class="form-control" readonly />
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="row">
					<label class="col-md-4 text-right">Exam Duration <span class="text-danger">*</span></label>
					<div class="col-md-8">
						<select name="online_exam_duration" id="online_exam_duration" class="form-control">
							<option value="">Select</option>
							<option value="5">5 Minute</option>
							<option value="5">15 Minute</option>
							<option value="30">20 Minute</option>
							<option value="30">30 Minute</option>
							<option value="60">1 Hour</option>
							<option value="120">2 Hour</option>
						</select>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="row">
					<label class="col-md-4 text-right">Total Question <span class="text-danger">*</span></label>
					<div class="col-md-8">
						<select name="total_question" id="total_question" class="form-control">
							<option value="">Select</option>
							<option value="5">5 Question</option>
							<option value="5">10 Question</option>
							<option value="10">15 Question</option>
							<option value="25">25 Question</option>
							<option value="50">50 Question</option>
						</select>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="row">
					<label class="col-md-4 text-right">Right Answer Marks <span class="text-danger">*</span></label>
					<div class="col-md-8">
						<select name="marks_per_right_answer" id="marks_per_right_answer" class="form-control">
							<option value="">Select</option>
							<option value="1">+1 Mark</option>
							<option value="2">+2 Mark</option>
							<option value="3">+3 Mark</option>
							<option value="4">+4 Mark</option>
						</select>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="row">
					<label class="col-md-4 text-right">Wrong Answer Marks <span class="text-danger">*</span></label>
					<div class="col-md-8">
						<select name="marks_per_wrong_answer" id="marks_per_wrong_answer" class="form-control">
							<option value="">Select</option>
							<option value="1">0 Mark</option>
							<option value="1">-0.5 Mark</option>
							<option value="1">-1 Mark</option>
							<option value="1">-1.5 Mark</option>
							<option value="2">-2 Mark</option>
						</select>
					</div>
				</div>
			</div>
			<input type="submit" name="question_button_action" id="question_button_action" class="addbtn btn btn-success btn-sm" value="Add" />
		</form>

	</div>
</div>
</div>

<?php view('footer') ?>

<script>

// Remove any existing active classes
$('#dashboard').removeClass('active');
// Add active class to clicked link
$('#sh').addClass('active');

// document.getElementsByClassName('alert-success')[0].style.marginLeft = "283px"
setTimeout(function() {
    document.getElementsByClassName('alert-success')[0].style.display = "none";
  }, 5000);

	var date = new Date();

	date.setDate(date.getDate());

	$('#online_exam_datetime').datetimepicker({
		startDate: date,
		format: 'yyyy-mm-dd hh:ii',
		autoclose: true
	});

	$('#exam_form').parsley();

	function reset_form()
	{
		$('#question_button_action').val('Add');
		$('#exam_form')[0].reset();
		$('#exam_form').parsley().reset();
	}


	$('#exam_form').on('submit', function(event) {
		// event.preventDefault();

		$('#online_exam_title').attr('required', 'required');

		$('#online_exam_subject').attr('required', 'required');

		$('#online_exam_datetime').attr('required', 'required');

		$('#online_exam_duration').attr('required', 'required');

		$('#total_question').attr('required', 'required');

		$('#marks_per_right_answer').attr('required', 'required');

		$('#marks_per_wrong_answer').attr('required', 'required');
		if ($('#exam_form').parsley().validate()) {
			$.ajax({
				url: "addexam.php",
				method: "POST",
				data: $(this).serialize(),
				dataType: "json",
				beforeSend:function(){
					$('#question_button_action').attr('disabled', 'disabled');
					$('#question_button_action').val('Validate...');
				},
				success:function(data)
				{
					console.log(data);
					reset_form();

					$('#question_button_action').attr('disabled', false);

					$('#question_button_action').val('Add');
				}
			});
			console.log("FV");
		}
	});
</script>

