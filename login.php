<?php

include 'includes/config.php';

$json = file_get_contents("php://input");
$response = array();
$data = json_decode($json, TRUE);

if (isset($data) && isset($data[GET_KEY_SEPARATOR . KEY_USERNAME]) && isset($data[GET_KEY_SEPARATOR . KEY_PASSWORD])) {

    if ($user->login($data[GET_KEY_SEPARATOR . KEY_USERNAME], $data[GET_KEY_SEPARATOR . KEY_PASSWORD])) {
            $response[KEY_SUCCESS] = ERROR_NIL;
            $response[KEY_MESSAGE] = "Logged In successfully!";
            $response[KEY_USERID] = $_SESSION[SESSION_ID];
            $response[KEY_TOKEN] = $_SESSION[KEY_TOKEN];
    } else {
        $response[KEY_SUCCESS] = $_SESSION[KEY_SUCCESS];
        $response[KEY_MESSAGE] = $_SESSION[KEY_MESSAGE];
    }
} else {
    $response[KEY_SUCCESS] = ERROR_EMPTY;
    $response[KEY_MESSAGE] = ERROR_EMPTY_MESSAGE;
}

echo json_encode($response, JSON_FORCE_OBJECT);
?>