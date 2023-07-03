
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="./public/assets/css/sidebar.css?v=18">
    <link rel="stylesheet" href="./public/assets/css/addexam.css?v=5">
    <link rel="stylesheet" href="./public/assets/css/review.css?v=2">
    <link rel="stylesheet" href="./public/assets/css/bootstrap-datetimepicker.css?v=2">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/guillaumepotier/Parsley.js@2.9.1/dist/parsley.js"></script>
    <script src="./public/assets/js/bootstrap-datetimepicker.js?v=2"></script>
    <title><?= $title ?? 'Home' ?></title>
    <style>
        .alert-success{
            width:50%;
            margin:auto;
            margin-left:520px;
        } 
    </style>
</head>
<body>
<main>
    <?php flash() ?>
<div class="wrapper">
    <div class="sidebar"> 
            <div class="profile">
                            <img src="https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg" alt="profile_picture">
                            <h3><?= current_user() ?></h3>
                            <p>Student</p>
                        </div>
            <ul>
                <li>
                    <a href="viewupcoming.php" id="upcoming">
                        <span class="icon"><i class="fas fa-desktop"></i></span>
                        <span class="item">Exam List</span>
                    </a>
                </li>
                <li>
                    <a href="result.php" id="res">
                        <span class="icon"><i class="fas fa-tachometer-alt"></i></span>
                        <span class="item">Result</span>
                    </a>
                </li>
                <li>
                    <a href="logout.php">
                        <span class="icon"><i class="fas fa-cog"></i></span>
                        <span class="item">Log Out</span>
                    </a>
                </li>
            </ul>
        </div>

