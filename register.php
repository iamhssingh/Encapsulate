<?php

require_once 'includes/config.php';

$json=file_get_contents("php://input");

$response = array();
// Create a data variable after decoding jSON
$data = json_decode($json, TRUE);

if (isset($data) && strlen($data[GET_KEY_SEPARATOR . KEY_USERNAME])>3 && strlen($data[GET_KEY_SEPARATOR . KEY_FULLNAME])>3 &&  strlen($data[GET_KEY_SEPARATOR . KEY_EMAIL])>3 && strlen($data[GET_KEY_SEPARATOR . KEY_PASSWORD])>7) {
    // && strlen($data[GET_KEY_SEPARATOR . KEY_USERNAME])>3 && strlen($data[GET_KEY_SEPARATOR . KEY_FULLNAME])>3 &&  strlen($data[GET_KEY_SEPARATOR . KEY_EMAIL])>3 && isset($data[GET_KEY_SEPARATOR . KEY_PASSWORD])>7

    // Create Variables for jSON data
    $username = $data[GET_KEY_SEPARATOR . KEY_USERNAME];
    $fullname = $data[GET_KEY_SEPARATOR . KEY_FULLNAME];
    $email = $data[GET_KEY_SEPARATOR . KEY_EMAIL];
    $password = $data[GET_KEY_SEPARATOR . KEY_PASSWORD];
    
    // Check if user already exists
    if(!$user->is_uname_unique($username)){
        $error = "UserName is not unique!";
    }
    
    // Check if email already exists
    elseif(!$user->is_uemail_unique($email)){
        $error = "EMailID is already in use!";
    }

    //if no errors have been created carry on
    if (!isset($error)) {

        //hash the password
        $hashedpassword = $user->password_hash($password, PASSWORD_BCRYPT);

        //create the activasion code
        $activasion = md5(uniqid(rand(), true));

        try {
            //insert into database with a prepared statement
            $stmt = $db->prepare('INSERT INTO user_master (userUName, userPassword, userEmail, isActive, userName, userCDate) VALUES (:username, :password, :email, :active, :fname, :cdate)');
            $stmt->execute(array(
                ':username' => $username,
                ':password' => $hashedpassword,
                ':email' => $email,
                ':fname' => $fullname,
                ':cdate' => date('Y-m-d H:i:s'),
                ':active' => 1
            ));
            $id = $db->lastInsertId('userID');

            // send email
            // TO DO 
            
            $response[KEY_SUCCESS] = ERROR_NIL;
            $response[KEY_MESSAGE]= "Sign Up successful! Please check your email to activate your account!";

        } catch (PDOException $e) {
            $response[KEY_SUCCESS] = ERROR_DATABASE;
            $response[KEY_MESSAGE] = ERROR_DATABASE_MESSAGE . $e->getMessage();
        }
    }else{
        $response[KEY_SUCCESS] = ERROR_SIGNUP;
        $response[KEY_MESSAGE] = $error;
    }

} else {
    $response[KEY_SUCCESS] = ERROR_EMPTY;
    $response[KEY_MESSAGE] = ERROR_EMPTY_MESSAGE;
}

echo json_encode($response, JSON_FORCE_OBJECT);
?>