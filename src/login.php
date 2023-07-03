<?php

if (is_user_logged_in()) {
    redirect_to('index.php');
}


$inputs = [];
$errors = [];

if (is_post_request()) {

    // sanitize & validate user inputs
    [$inputs, $errors] = filter($_POST, [
        'username' => 'string | required',
        'password' => 'string | required',
        'role' => 'string | required'
    ]);

    // if validation error
    if ($errors) {
        redirect_with('login.php', [
            'errors' => $errors,
            'inputs' => $inputs
        ]);
    }


    // if login fails
    if (!login($inputs['username'], $inputs['password'],$inputs['role'])) {

        $errors['login'] = 'Invalid username or password';

        redirect_with('login.php', [
            'errors' => $errors,
            'inputs' => $inputs
        ]);
    }

    // login successfully
    else if($inputs['role']=='Faculty') {
        redirect_to('indexFaculty.php');
    }
    else if($inputs['role']=='User') {
        redirect_to('viewupcoming.php');
    }

} else if (is_get_request()) {
    [$errors, $inputs] = session_flash('errors', 'inputs');
}
?>


