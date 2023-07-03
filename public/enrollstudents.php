<?php
require __DIR__ . '/../src/bootstrap.php';
require_once '../mailer/vendor/autoload.php';
require_login();
error_reporting(E_ALL);
ini_set('display_errors', '1');
?>
<?php view('sidebar') ?>
<div class="right-panel">
    <?php
    if (is_post_request()) {
        ini_set('upload_max_filesize', '10M');
        ini_set('post_max_size', '20M');
        $exam = $_POST['form_id'];
        $file = $_FILES['file'];
        if ($file['error'] === UPLOAD_ERR_OK) {
            $handle = fopen($file['tmp_name'], 'r');
            $counter = 0;
            $enrollment = array();
            while (($data = fgetcsv($handle)) !== false) {
                array_push($enrollment, $data);
            }
            fclose($handle);
            foreach ($enrollment as $enroll) {
                foreach ($enroll as $student) {
                    $sql = 'INSERT INTO enroll_table(user_id,exam_id,attendance_status) VALUES (:ui,:ei,:as)';
                    $sql2 = 'SELECT COUNT(*) as count FROM enroll_table WHERE user_id = :user_id AND exam_id = :exam_id';
                    $statement = db()->prepare($sql2);
                    $statement->bindValue(':user_id', $student);
                    $statement->bindValue(':exam_id', $exam);
                    $statement->execute();
                    $result = $statement->fetch(PDO::FETCH_ASSOC);
                    $count = $result['count'];
                    if($count==0){
                    $statement = db()->prepare($sql);
                    $statement->bindValue(':ui', $student);
                    $statement->bindValue(':ei', $exam);
                    $statement->bindValue(':as', 'Absent');
                    $statement->execute();
                    }
                }
            }
            $sql = "UPDATE exam_table SET online_exam_status = :new_value WHERE online_exam_id=:id";
            $sql2 = "SELECT QUESTIONS FROM EXAM_TABLE WHERE ONLINE_EXAM_ID=:id";
            $statement2 = db()->prepare($sql2);
            $statement2->bindValue(':id', $_POST['form_id']);
            $statement2->execute();
            $result = $statement2->fetch();

            $sql3 = "UPDATE exam_table SET ENROLLMENT=:q WHERE ONLINE_EXAM_ID=:id";
            $statement3 = db()->prepare($sql3);
            $statement3->bindValue(':q', '1');
            $statement3->bindValue(':id', $_POST['form_id']);
            $statement3->execute();

            $statement = db()->prepare($sql);
            $statement->bindValue(':id', $_POST['form_id']);

            if ($result['QUESTIONS'] == '1') {
                $statement->bindValue(':new_value', 'Created');
                $statement->execute();
            }

            $flashId = 'flash_' . uniqid();
            flash($flashId, "Students Enrolled successfully", FLASH_SUCCESS);
        } else {
            // Handle file upload error here
            echo 'Error uploading file.';
        }
    }

    ?>
    <?php
    $sql = 'SELECT * FROM exam_table WHERE admin_name=:current_user and ENROLLMENT=:ENROLLMENT';
    $statement = db()->prepare($sql);
    $statement->bindValue(':current_user', current_user());
    $statement->bindValue(':ENROLLMENT', '0');
    $statement->execute();
    $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
    flash();

    echo '<div class="row" style="margin:0;">';
    foreach ($rows as $row) {
        echo '<div class="col-4 my-3">';
        echo '<div class="card">';
        echo '<div class="card-body">';
        echo '<h5 class="card-title"> Subject : ' . $row['exam_subject'] . '</h5>';
        echo '<h5 class="card-title"> exam : ' . $row['exam_title'] . '</h5>';
        echo '<p class="card-text"> Date and Time : ' . $row['exam_datetime'] . '</p>';
        echo '<p class="card-text"> Total Question : ' . $row['total_question'] . '</p>';
        echo '<form  action="enrollstudents.php" method="post" enctype="multipart/form-data" id="' . $row['exam_title'] . '">';
        echo '<div class="form-group">';
        echo '<input type="file" name="file" class="form-control-file" accept=".csv,text/csv" style="width: 220px;margin-left: -78px;">';
        echo '<input type="hidden" name="form_id" value="' . $row['online_exam_id'] . '">';
        echo '</div>';
        echo '<button type="submit" class="btn btn-primary" style="  margin-left: -78px;">Upload File</button>';
        echo '</form>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';

    if(count($rows)==0){
        echo '<div style="font-size:40px;">NO ENROLLMENT PENDING</div>';
    }
    ?>
</div>

<?php view('footer') ?>

<script>
    // Remove any existing active classes
    $('#dashboard').removeClass('active');
    // Add active class to clicked link
    $('#enrstu').addClass('active');

    document.getElementsByClassName('alert-success')[0].style.marginLeft = "283px";
</script>