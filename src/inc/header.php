<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="./public/assets/css/auth.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/parsleyjs"></script>
<link rel="stylesheet" type="text/css" href="./public/assets/css/TimeCircles.css?v=7;">
<script src="./public/assets/css/TimeCircles.js"></script>


    <title><?= $title ?? 'Home' ?></title>
    <style>
        #rolelabel{
    margin-left: 70px;
    font-size: 20px;
        }

    .next{
      color:white;
    }

  
  
    </style>
    <script>
  setTimeout(function() {
    document.getElementById('flash-message').style.display = 'none';
  }, 5000);
</script>
</head>
<body>
<main>
<div id="flash-message">
  <?php flash() ?>
</div>



