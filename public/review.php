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
                    <h3 class="panel-title">Exam Review</h3>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <?php
                $sql = 'SELECT online_exam_id,exam_subject,exam_title,exam_datetime,total_question FROM exam_table WHERE online_exam_id=:id';
                $statement = db()->prepare($sql);
                $statement->bindValue(':id', $_GET['online_exam_id']);
                $statement->execute();
                $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

                foreach ($rows as $row) {
                    $sql = 'SELECT user_id, SUM(CAST(marks AS SIGNED)) AS total_marks 
                    FROM student_exam 
                    WHERE exam_id = :id 
                    AND user_id=:user_id';
					$statement = db()->prepare($sql);
					$statement->bindValue(':id', $row['online_exam_id']);
					$statement->bindValue(':user_id', current_user());
					$statement->execute();
					$marks = $statement->fetchAll(PDO::FETCH_ASSOC);
                   
                    $count = 1;
                    echo "<b>Subject : " . $row['exam_subject'] . "</b><br>";
                    echo "<b>Exam    : " . $row['exam_title'] . "</b><br>";
                    echo "<b>Exam Date and Time    : " . $row['exam_datetime'] . "</b><br>";
                    echo "<b>Marks    : " . $marks[0]['total_marks'] . " Out of ".$row['total_question']."</b><br>";
                    echo "<br>";

                    $sql = 'SELECT question_id,QUESTION_TITLE,ANSWER_OPTION FROM QUESTION_TABLE WHERE ONLINE_EXAM_ID=:id';
                    $statement = db()->prepare($sql);
                    $statement->bindValue(':id', $row['online_exam_id']);
                    $statement->execute();
                    $questions = $statement->fetchAll(PDO::FETCH_ASSOC);
                    
                    $sql = 'SELECT OPTION_NUMBER,OPTION_TITLE FROM OPTION_TABLE WHERE QUESTION_ID=:id';
                    $statement = db()->prepare($sql);
                    echo "<hr>";
                    foreach ($questions as $question) {
                        $statement->bindValue(':id', $question['question_id']);
                        $statement->execute();
                        $options = $statement->fetchAll(PDO::FETCH_ASSOC);
                        
                        $sql1='SELECT USER_ANSWER_OPTION from STUDENT_EXAM where exam_id=:id and question_id=:question_id';
                        $statement1 = db()->prepare($sql1);
                        $statement1->bindValue(':id',$_GET['online_exam_id']);
                        $statement1->bindValue(':question_id', $question['question_id']);
                        $statement1->execute();
                    $myanswer = $statement1->fetchAll(PDO::FETCH_ASSOC);
                        echo "<div id='reviewquestion'>" . $count . '.' . $question['QUESTION_TITLE'] . "</div>";
                        $count++;
                        echo "<div>" . 'A . ' . $options[0]['OPTION_TITLE'] . "</div>";
                        echo "<div>" . 'B . ' . $options[1]['OPTION_TITLE'] . "</div>";
                        echo "<div>" . 'C . ' . $options[2]['OPTION_TITLE'] . "</div>";
                        echo "<div>" . 'D . ' . $options[3]['OPTION_TITLE'] . "</div>";
                        echo "<div>" . 'Correct Answer : ' . $question['ANSWER_OPTION'] . '<br>';
                        if ($question['ANSWER_OPTION'] == 'A') {
                            echo $options[0]['OPTION_TITLE'];
                        } else if ($question['ANSWER_OPTION'] == 'B') {
                            echo $options[1]['OPTION_TITLE'];
                        } else if ($question['ANSWER_OPTION'] == 'C') {
                            echo $options[2]['OPTION_TITLE'];
                        } else if ($question['ANSWER_OPTION'] == 'D') {
                            echo $options[3]['OPTION_TITLE'];
                        }
                        echo "</div>";
                        if($myanswer[0]['USER_ANSWER_OPTION']==$question['ANSWER_OPTION'])
                        echo "<div style='background-color:lightgreen;'> Correct";
                        else
                        echo "<div style='background-color:#ffcccb;'> Incorrect";
                        echo "<div>" . 'Your Answer : ' . $myanswer[0]['USER_ANSWER_OPTION'] . '<br>';
                        if ($myanswer[0]['USER_ANSWER_OPTION'] == 'A') {
                            echo $options[0]['OPTION_TITLE'];
                        } else  if ($myanswer[0]['USER_ANSWER_OPTION'] == 'B') {
                            echo $options[1]['OPTION_TITLE'];
                        } else  if ($myanswer[0]['USER_ANSWER_OPTION'] == 'C') {
                            echo $options[2]['OPTION_TITLE'];
                        } else  if ($myanswer[0]['USER_ANSWER_OPTION'] == 'D') {
                            echo $options[3]['OPTION_TITLE'];
                        }

                        echo "</div>";
                        echo "</div>";
                    }
                    echo "<br>";
                }
                ?>

            </div>
        </div>
    </div>
</div>

<?php view('footer') ?>