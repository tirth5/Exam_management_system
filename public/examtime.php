<?php
require __DIR__ . '/../src/bootstrap.php';
view('header', ['title' => 'Exam']);
require_login();
?>

<?php
$count=0;
$questionsofexam=array();
$optionsofexam=array();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    global $count, $questionsofexam, $optionsofexam;

    $exam_id = $_GET['online_exam_id'];
    $exam_status = '';
    $remaining_minutes = '';

    $sql = 'SELECT * from exam_table WHERE online_exam_id=:exam_id';
    $statement = db()->prepare($sql);
    $statement->bindValue(':exam_id', $exam_id);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    foreach ($result as $row) {
        $exam_status = $row['online_exam_status'];
        $exam_start_time = $row['exam_datetime'];
        $duration = $row['exam_duration'] . ' minute';
        $exam_end_time = strtotime($exam_start_time . '+' . $duration);
        $exam_end_time = date('Y-m-d H:i:s', $exam_end_time);
        $remaining_minutes = strtotime($exam_end_time) - time();
    }
    $sql = "UPDATE enroll_table SET attendance_status = :attendance_status WHERE user_id = :user_id AND exam_id = :exam_id";
    $statement = db()->prepare($sql);
    $statement->bindValue(':attendance_status', 'Present');
    $statement->bindValue(':user_id', current_user());
    $statement->bindValue(':exam_id', $exam_id);
    $statement->execute();

    $sql='SELECT * FROM question_table WHERE online_exam_id = :exam_id ORDER BY RAND() LIMIT :tot';
    $statement = db()->prepare($sql);
    // print_r($result[0]['total_question']);
    $statement->bindValue(':exam_id',$exam_id);
    $statement->bindValue(':tot',$result[0]['total_question'],PDO::PARAM_INT);
    $statement->execute();   
    $questionsofexam = $statement->fetchAll(PDO::FETCH_ASSOC);
    // print_r($questionsofexam);
    $output='';
    $optionsofexam=array();
    foreach ($questionsofexam as $row) {   
     $count=0;
        $sql = "
        SELECT * FROM option_table 
        WHERE question_id = :question_id ORDER BY RAND()";
        $statement = db()->prepare($sql);
        $statement->bindValue(':question_id', $row['question_id']);
        $statement->execute();
        $sub_result = $statement->fetchAll(PDO::FETCH_ASSOC);
        array_push($optionsofexam,$sub_result);
    }
    render_question($questionsofexam[$count],$optionsofexam[$count],$count); 
}   

// if ($_SERVER['REQUEST_METHOD']=='POST') {
//     global $count, $questionsofexam, $optionsofexam;

// echo $count;
// $count++;
// var_dump($questionsofexam);
// if($count<count($questionsofexam)){
//     render_question($questionsofexam[$count],$optionsofexam[$count],$count);
// }
// else{
//     // header('Location:viewupcoming.php');
// }   

// }
?>


<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <div id="single_question_area">
                    <?php 
                    function render_question($question, $options, $count){ 
                        $count=$count+1;
                        $output = '';
                        $output .= '<h1>' .$count.'.'. addslashes($question["question_title"]) . '</h1>';
                        $output .= '<hr />';
                        $output .= '<br />';
                        $output .= '<div class="row">';
                        $output .= '<div class="col-md-6" style="margin-bottom:32px;">';
                    
                        foreach($options as $option){
                            $output .= '<div class="radio">';
                            $output .= '<label><h4><input type="radio" name="option_1" class="answer_option" data-question_id="' . $question["question_id"] . '" id-data="' . $count . '"/>&nbsp;' . addslashes($option["option_title"]) . '</h4></label>';
                            $output .= '</div>';
                        }
                    
                        $output .= '</div></div>';
                        echo $output;
                    }
                    ?> 
                    <div align="center">
                        <form method="post">
                            <button type="button" name="next" class="btn btn-warning btn-lg next" style="color:white;">Next</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <br />
    </div>
    <div class="col-md-4">
        <br />
        <div align="center">
            <div id="exam_timer" data-timer="<?php echo $remaining_minutes; ?>" style="max-width: 100%; height: 700px; width: 1200px;"></div>
        </div>
        <br />
        <div id="user_details_area"></div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/timecircles/1.5.3/TimeCircles.min.js"></script>
<script>
    //  window.open('http://localhost/php_innovative/viewspecificexam.php?online_exam_id=1', '_blank', 'fullscreen=yes,toolbar=no,menubar=no');


    $(document).ready(function() {
        $('.next').click(function() {
            $('form').submit();
        });
        $("#exam_timer").TimeCircles({
            time: {
                Days: {
                    show: false
                },
                Hours: {
                    show: false
                }
            }
        });

        setInterval(function() {
            // console.log($("exam_timer").TimeCircles())
            var remaining_second = $("#exam_timer").TimeCircles().getTime();

            if (remaining_second < 1) {
                // alert('Exam time over');
                // location.reload();
            }
        }, 1000);


        $(document).on('click', '.answer_option', function() {
            var question_id = $(this).data('question_id');
            console.log(question_id);
            var answer_option = $(this).data('id');

            $.ajax({
                url: "viewspecificexam.php",
                method: "POST",
                data: {
                    question_id: question_id,
                    answer_option: answer_option,
                    exam_id: exam_id,
                    page: 'view_exam',
                    action: 'answer'
                },
                success: function(data) {

                }
            })
        });
    });
</script>