<?php
require __DIR__ . '/../src/bootstrap.php';
view('header', ['title' => 'Exam']);
require_login();
?>

<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['page'] == 'view_exam') {
        if ($_POST['action'] == 'load_question') {
            if (isset($_SESSION['E' . $_POST['exam_id']])) {
                if ($_SESSION['previous_id'] != $_POST['question_id']) {

                    $_SESSION['E' . $_POST['exam_id']] += 1;
                }
            } else {
                $_SESSION['E' . $_POST['exam_id']] = 1;
            }
            $_SESSION['previous_id'] = $_POST['question_id'];
            if ($_POST['question_id'] == '') {

                $sql = "SELECT COUNT(*) FROM STUDENT_EXAM WHERE user_id = :user_id AND exam_id = :exam_id";
$statement = db()->prepare($sql);
$statement->bindParam(':user_id', $_SESSION['username']);
$statement->bindParam(':exam_id', $_POST['exam_id']);
$statement->execute();
$count = $statement->fetchColumn();

if($count==0){

                $sql = "INSERT INTO STUDENT_EXAM (user_id, exam_id, question_id, user_answer_option, marks) VALUES (:user_id, :exam_id, :question_id, :user_answer_option, :marks)";
                $statement = db()->prepare($sql);
                $statement->bindParam(':user_id', $_SESSION['username']);
                $statement->bindParam(':exam_id', $_POST['exam_id']);

                $sql = "SELECT question_id FROM question_table WHERE online_exam_id = :exam_id";
                $statement2 = db()->prepare($sql);
                $statement2->bindParam(':exam_id', $_POST['exam_id']);
                $statement2->execute();
                $results = $statement2->fetchAll(PDO::FETCH_ASSOC);
                $initial = 'E';
                $inimarks = 0;
                echo $results;
                foreach ($results as $row) {
                    $statement->bindParam(':question_id', $row['question_id']);
                    $statement->bindParam(':user_answer_option', $initial);
                    $statement->bindParam(':marks', $inimarks);
                    $statement->execute();
                }
            }
                $sql = "SELECT * FROM question_table WHERE online_exam_id = :exam_id 
                ORDER BY question_id ASC 
                LIMIT 1
                ";
                $statement = db()->prepare($sql);
                $statement->bindValue(':exam_id', $_POST['exam_id']);
            } 
            else {
                $sql = "SELECT * FROM question_table WHERE question_id = :qid";
                $statement = db()->prepare($sql);
                $statement->bindValue(':qid', $_POST['question_id']);
            }
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            $output = '';
            foreach ($result as $row) {
                $output .= '
                <div id="quest">
                    <h1> ' . $_SESSION['E' . $_POST['exam_id']] . '.' . $row["question_title"] . '</h1>
                    <hr />
                    <br />
                    <div class="row">
                    ';
                $sql = "
                    SELECT * FROM option_table 
                    WHERE question_id = :question_id";
                $statement = db()->prepare($sql);
                $statement->bindValue(':question_id', $row['question_id']);
                $statement->execute();
                $sub_result = $statement->fetchAll(PDO::FETCH_ASSOC);
                foreach ($sub_result as $sub_row) {
                    $output .= '
                    <div class="col-md-6" style="margin-bottom:32px;">
                    <div class="radio">
                        <label><h4><input type="radio" name="option_1" class="answer_option" data-question_id="' . $row["question_id"] . '" data-id="' . $sub_row['option_number'] . '"/>&nbsp;' . $sub_row["option_title"] . '</h4></label>
                    </div>
                </div>
                        ';
                }
                $output .= '
                    </div>
                    ';

                $next_id = '';

                $sql = "SELECT question_id FROM question_table 
                        WHERE question_id > :question_id 
                        AND online_exam_id = :exam_id 
                        ORDER BY question_id ASC 
                        LIMIT 1";
                $stmt = db()->prepare($sql);
                $stmt->bindValue(':question_id', $row['question_id']);
                $stmt->bindValue(':exam_id', $_POST['exam_id']);
                $stmt->execute();
                $next_result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($next_result as $next_row) {
                    $next_id = $next_row['question_id'];
                }

                $if_next_disable = '';
                if ($next_id == "") {
                    // $sql = "UPDATE EXAM_TABLE SET ONLINE_EXAM_STATUS=:STATUS1 WHERE ONLINE_EXAM_ID=:ID";
                    // $statement = db()->prepare($sql);
                    // $statement->bindValue(':ID', $_POST['exam_id']);
                    // $statement->bindValue(':STATUS1', 'Completed');
                    // $statement->execute();
                    $output .= '
        <br /><br />
        <div align="center">
        <button type="button" name="next" class="btn btn-warning btn-lg next" id="done" style="color:white;"><a style="color:white;" href="viewupcoming.php">Submit</a></button>
        </div>
        <br /><br />
    </div>';
                }
                else{
                $output .= '
    <br /><br />
    <div align="center">
        <button type="button" name="next" class="btn btn-warning btn-lg next" id="' . $next_id . '" ' . $if_next_disable . ' style="color:white;' . ($if_next_disable ? 'background-color: gray 
        !important; cursor: not-allowed;' : '') . '">Next</button>
    </div>
    <br /><br />
</div>';
                }
            }
            echo $output;
        }
    }
    if ($_POST['action'] == 'answer') {
        $sql = "SELECT marks_per_right_answer FROM exam_table WHERE online_exam_id = :id";
        $statement = db()->prepare($sql);
        $statement->bindValue(':id', $_POST['exam_id']);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $row) {
            $exam_right_answer_mark = $row['marks_per_right_answer'];
        }



        $sql = "SELECT marks_per_wrong_answer FROM exam_table WHERE online_exam_id = :id";
        $statement = db()->prepare($sql);
        $statement->bindValue(':id', $_POST['exam_id']);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $row) {
            $exam_wrong_answer_mark = $row['marks_per_wrong_answer'];
        }



        $sql = "SELECT answer_option FROM question_table WHERE question_id = :question_id";
        $statement = db()->prepare($sql);
        $statement->bindValue(':question_id', $_POST['question_id']);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $row) {
            $original_answer = $row['answer_option'];
        }
        $marks = 0;
        if ($original_answer == $_POST['answer_option']) {
            $marks = '+' . $exam_right_answer_mark;
        } else {
            $marks = '-' . $exam_wrong_answer_mark;
        }
        echo $marks;

        $sql = "
        UPDATE student_exam 
        SET user_answer_option = :user_answer_option, marks = :marks 
        WHERE user_id = :user_id  
        AND exam_id = :exam_id
        AND question_id = :question_id;
    ";
        $statement = db()->prepare($sql);
        $statement->bindValue(':user_id', current_user());
        $statement->bindValue(':exam_id', $_POST['exam_id']);
        $statement->bindValue(':question_id', $_POST['question_id']);
        $statement->bindValue(':user_answer_option', $_POST['answer_option']);
        $statement->bindValue(':marks', $marks);
        $statement->execute();
    }
}

?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
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
}
?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <div id="single_question_area"></div>
            </div>
        </div>
        <br />
    </div>
    <div class="col-md-4">
        <br />
        <div align="center">
            <div id="user_details" style="font-weight:bold; font-size: 30px;"><?php echo "Roll No. : ".current_user() ?></div>
            <div id="exam_timer" data-timer="<?php echo $remaining_minutes;  ?>" style="max-width: 100%; height: 700px; width: 1200px;"></div>
        </div>
        <br />
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/timecircles/1.5.3/TimeCircles.min.js"></script>
<script>
    //  window.open('http://localhost/php_innovative/viewspecificexam.php?online_exam_id=1', '_blank', 'fullscreen=yes,toolbar=no,menubar=no');

    $(document).ready(function() {
        if (<?php echo isset($exam_id) ? 'true' : 'false'; ?>) {
            exam_id = "<?php echo $exam_id; ?>"
            load_question('', exam_id);
        }

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
            console.log(remaining_second)
            if (remaining_second < 1) {
                <?php 
                    $sql = "UPDATE EXAM_TABLE SET ONLINE_EXAM_STATUS=:STATUS1 WHERE ONLINE_EXAM_ID=:ID";
                    $statement = db()->prepare($sql);
                    $statement->bindValue(':ID', $exam_id);
                    $statement->bindValue(':STATUS1', 'Completed');
                    $statement->execute();
                    ?>
                window. location.href="viewupcoming.php";
            }
        }, 1000);

        function load_question(question_id = '', exam_id) {
            $.ajax({
                url: "viewspecificexam.php",
                method: "POST",
                data: {
                    exam_id: exam_id,
                    question_id: question_id,
                    page: 'view_exam',
                    action: 'load_question'
                },
                success: function(data) {
                    var data = $(data).find('#quest').html();
                    $('#single_question_area').html(data);
                }
            })
        }


        $(document).on('click', '.next', function() {
            var question_id = $(this).attr('id');
            console.log(question_id);
            load_question(question_id, exam_id);
        });

        $(document).on('click', '.answer_option', function() {
            var question_id = $(this).data('question_id');
            var answer_option = $(this).data('id');

            $.ajax({
                url: "viewspecificexam.php",
                method: "POST",
                data: {
                    question_id: question_id,
                    answer_option: answer_option,
                    exam_id: exam_id,
                    action: 'answer'
                },
                success: function(data) {}
            })
        });
    });
</script>