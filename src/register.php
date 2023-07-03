<?php

$errors = [];
$inputs = [];

if (is_user_logged_in()) {
    redirect_to('index.php');
}

if (is_post_request()) {

    $fields = [
        'email' => 'email | required | email | unique: users, email',
        'password' => 'string | required | secure',
        'password2' => 'string | required | same: password',
        'agree' => 'string | required'
    ];
    
    // custom messages
    $messages = [
        'password2' => [
            'required' => 'Please enter the password again',
            'same' => 'The password does not match'
        ],
        'agree' => [
            'required' => 'You need to agree to the term of services to register'
        ]
    ];
    
    [$inputs, $errors] = filter($_POST, $fields, $messages);

    if ($errors) {
        redirect_with('register.php', [
            'inputs' => $inputs,
            'errors' => $errors
        ]);
    }

    $email = $_POST['email'];
    if (substr($email, 8) != '@nirmauni.ac.in') {
        $flashId = 'flash_' . uniqid();
        flash($flashId, 'Invalid E-mail ID,Please Enter Your Nirma E-mail ID', FLASH_ERROR);
        header('Location:' . 'register.php');
        exit;
    }

    var_dump($_POST);
    $activation_code = bin2hex(random_bytes(16));

    $email = $_POST['email'];
    $sql = "SELECT EMAIL FROM USERS WHERE EMAIL = :email";
    $statement = db()->prepare($sql);
    $statement->bindValue(':email', $inputs['email']);

    $statement->execute();

    if ($statement->fetchColumn()) {
        $flashId = 'flash_' . uniqid();
        flash($flashId, 'E-mail already Exists if not registed by you then try again some time ', FLASH_ERROR);
        header('Location:' . 'register.php');
        exit;
    }


    if (register_user($inputs['email'], $inputs['password'], $activation_code)) {

        // send the activation email
        send_activation_email($inputs['email'], $activation_code);

        redirect_with_message(
            'login.php',
            'Please check your email to activate your account before signing in'
        );
    }
} else if (is_get_request()) {
    [$errors, $inputs] = session_flash('errors', 'inputs');
}
