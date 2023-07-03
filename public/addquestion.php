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
        $Questions=array();
        while (($data = fgetcsv($handle)) !== false) {
            if ($counter > 0) {
                array_push($Questions,$data);
            }
            $counter++;
        }
        fclose($handle);
        

        foreach($Questions as $Question){
            $sql = 'INSERT INTO question_table(online_exam_id,question_title,answer_option) VALUES (:online_exam_id,:question_title,:answer_option)';
    
            $statement = db()->prepare($sql);
        
            $statement->bindValue(':online_exam_id',$_POST['form_id']);
            $statement->bindValue(':question_title',$Question[0]);
            $statement->bindValue(':answer_option',$Question[5]);
            
            $statement->execute();
            $lastInsertId = db()->lastInsertId();

            $op=array('*','A','B','C','D');
            for($i=1;$i<=4;$i++){
            $sql = 'INSERT INTO option_table(question_id,option_number,option_title) VALUES (:question_id,:option_number,:option_title)';
    
            $statement = db()->prepare($sql);
        
            $statement->bindValue(':question_id',$lastInsertId);
            $statement->bindValue(':option_number',$op[$i]);
            $statement->bindValue(':option_title',$Question[$i]);
            
            $statement->execute();
            }
        }

        $sql="UPDATE exam_table SET online_exam_status = :new_value WHERE online_exam_id=:id";
        $sql2="SELECT ENROLLMENT FROM EXAM_TABLE WHERE ONLINE_EXAM_ID=:id";
        $sql3="UPDATE exam_table SET QUESTIONS=:q WHERE ONLINE_EXAM_ID=:id" ;
        $statement3 = db()->prepare($sql3);
        $statement3->bindValue(':q','1');
        $statement3->bindValue(':id',$_POST['form_id']);
        $statement3->execute();

        $statement2 = db()->prepare($sql2);
        $statement2->bindValue(':id',$_POST['form_id']);
        $statement2->execute();
        $result = $statement2->fetch(); 

        $statement = db()->prepare($sql);
        $statement->bindValue(':id',$_POST['form_id']);
        if($result['ENROLLMENT']=='0')
        $statement->bindValue(':new_value','Enrollment Pending');
        else{
        $statement->bindValue(':new_value','Created');
        }
        $statement->execute();

        $flashId = 'flash_' . uniqid();
         
        $flashId = 'flash_' . uniqid();
        flash($flashId,"Questions Added successfully", FLASH_SUCCESS);
        // header('addquestion.php');
    }
    else {
        // Handle file upload error here
        echo 'Error uploading file.';
    }
}

?>
    <?php 
    $sql = 'SELECT * FROM exam_table WHERE admin_name=:current_user and QUESTIONS=:Q';
    $statement = db()->prepare($sql);
    $statement->bindValue(':current_user', current_user());
    $statement->bindValue(':Q', '0');
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
               echo '<form  action="addquestion.php" method="post" enctype="multipart/form-data" id="'. $row['exam_title'] .'">';
               echo '<div class="form-group">';
               echo '<input type="file" name="file" class="form-control-file" accept=".csv,text/csv" style="width: 220px;margin-left: -78px;">';
               echo '<input type="hidden" name="form_id" value="'. $row['online_exam_id'] .'">';
               echo '</div>';
               echo '<button type="submit" class="btn btn-primary" style="  margin-left: -78px;">Upload File</button>';
               echo '</form>';
               echo '</div>';
               echo '</div>';
               echo '</div>';
            }
    echo '</div>';
    if(count($rows)==0){
        echo '<div style="font-size:40px;">NO EXAMS PENDING</div>';
    }
    ?>
</div>

<?php view('footer') ?>

<script>     


// Remove any existing active classes
$('#dashboard').removeClass('active');
// Add active class to clicked link
$('#addq').addClass('active');

document.getElementsByClassName('alert-success')[0].style.marginLeft = "283px";
setTimeout(function() {
    document.getElementsByClassName('alert-success')[0].style.display = "none";
  }, 5000);
</script>