<?php

require __DIR__ . '/../src/bootstrap.php';

if (is_get_request()) {

        $user = find_unverified_user($_GET['activation_code'], $_GET['email']);

        // if user exists and activate the user successfully
        if ($user && activate_user($user['id'])) {
            redirect_with_message(
                'message.php',
                'You account has been activated successfully.Now you can sign in into sign in page'
            );
    }
}
 
// redirect to the register page in other cases
redirect_with_message(
    'register.php',
    'The activation link is not valid, please register again.',
    FLASH_ERROR
);


