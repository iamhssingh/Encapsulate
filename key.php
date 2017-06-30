<?php

// Include global config file
require_once 'includes/config.php';

// get the POST JSON data
$json = file_get_contents("php://input");

// prep a response array
$response = array();

// Create a data variable after decoding jSON
$data = json_decode($json, TRUE);

if (isset($data) && isset($data[GET_KEY_SEPARATOR . KEY_USERID]) && isset($data[GET_KEY_SEPARATOR . KEY_TOKEN])) {

    // put USER ID and last access ID into Session variable
    $_SESSION[SESSION_ID] = $data[GET_KEY_SEPARATOR . KEY_USERID];

    // Get last access ID and put it in Session variable
    $_SESSION[SESSION_LAID] = $user->get_laid();

    // Verify the user
    if ($user->verify_token($data[GET_KEY_SEPARATOR . KEY_TOKEN])) {

        // Put new token into response array
        $response[KEY_TOKEN] = $_SESSION[KEY_TOKEN];

        // Get the action
        $action = $data[GET_KEY_SEPARATOR . GET_KEY_ACTION];

        // Perform switch action
        switch ($action) {

            // Case ADD
            case GET_ACTION_VALUE_ADD:
                $keydata = $data[X_KEY . GET_KEY_SEPARATOR . KEY_X_Data];
                $keyinfo = $data[X_KEY . GET_KEY_SEPARATOR . KEY_X_INFO];
                $keyname = $data[X_KEY . GET_KEY_SEPARATOR . KEY_X_NAME];
                if ($user->verify_pwd($data[GET_KEY_SEPARATOR . KEY_PASSWORD]) && $user->add_key($keydata, $keyinfo, $keyname)) {
                    $response[KEY_SUCCESS] = ERROR_NIL;
                    $response[KEY_MESSAGE] = "Key added successfully!";
                } else {
                    $response[KEY_SUCCESS] = $_SESSION[KEY_SUCCESS];
                    $response[KEY_MESSAGE] = $_SESSION[KEY_MESSAGE];
                }
                $response[KEY_SUCCESS] = ERROR_NIL;
                $response[KEY_MESSAGE] = "Successfully sent the data!";
                break;

            // FOR DELETE
            case "delete":
                $keyid = $data[X_KEY . GET_KEY_SEPARATOR . KEY_X_ID];
                if ($user->verify_pwd($data[GET_KEY_SEPARATOR . KEY_PASSWORD]) && $user->del_key($keyid)) {
                    $response[KEY_SUCCESS] = ERROR_NIL;
                    $response[KEY_MESSAGE] = "Key removed successfully!";
                } else {
                    $response[KEY_SUCCESS] = $_SESSION[KEY_SUCCESS];
                    $response[KEY_MESSAGE] = $_SESSION[KEY_MESSAGE];
                }
                $response[KEY_SUCCESS] = ERROR_NIL;
                $response[KEY_MESSAGE] = "Successfully sent the data!";
                break;

            // FOR EDIT
            case "edit":
                $keydata = $data[X_KEY . GET_KEY_SEPARATOR . KEY_X_Data];
                $keyinfo = $data[X_KEY . GET_KEY_SEPARATOR . KEY_X_INFO];
                $keyname = $data[X_KEY . GET_KEY_SEPARATOR . KEY_X_NAME];
                $keyid = $data[X_KEY . GET_KEY_SEPARATOR . KEY_X_ID];
                if ($user->verify_pwd($data[GET_KEY_SEPARATOR . KEY_PASSWORD]) && $user->del_key($keyid) && $user->add_key($keydata, $keyinfo, $keyname)) {
                    $response[KEY_SUCCESS] = ERROR_NIL;
                    $response[KEY_MESSAGE] = "Key changed successfully!";
                } else {
                    $response[KEY_SUCCESS] = $_SESSION[KEY_SUCCESS];
                    $response[KEY_MESSAGE] = $_SESSION[KEY_MESSAGE];
                }
                $response[KEY_SUCCESS] = ERROR_NIL;
                $response[KEY_MESSAGE] = "Successfully sent the data!";
                break;

            // For SHOWING
            case "show":
                $key = array();
                if ($user->verify_pwd($data[GET_KEY_SEPARATOR . KEY_PASSWORD])) {
                    $keyfull = $user->get_key($data[X_KEY . GET_KEY_SEPARATOR . KEY_X_ID]);
//                    $key[X_KEY . KEY_X_NAME] = $keyfull[X_KEY . DB_X_NAME];
//                    $key[X_KEY . KEY_X_INFO] = $keyfull[X_KEY . DB_X_INFO];
//                    $key[X_KEY . KEY_X_CDate] = $keyfull[X_KEY . DB_X_CDate];
//                    $key[X_KEY . KEY_X_Data] = $keyfull[X_KEY . DB_X_Data];
                    $response[X_KEY . POST_KEY_X_DATA] = $keyfull[X_KEY . DB_X_DATA];
                } else {
                    $response[KEY_SUCCESS] = $_SESSION[KEY_SUCCESS];
                    $response[KEY_MESSAGE] = $_SESSION[KEY_MESSAGE];
                }
                $response[KEY_SUCCESS] = ERROR_NIL;
                $response[KEY_MESSAGE] = "Successfully sent the data!";
                break;
            default :
                $response[KEY_SUCCESS] = ERROR_EMPTY;
                $response[KEY_MESSAGE] = ERROR_EMPTY_MESSAGE;
                break;
        }
    } else {

        //IF Login Verification fails
        $response[KEY_SUCCESS] = $_SESSION[KEY_SUCCESS];
        $response[KEY_MESSAGE] = $_SESSION[KEY_MESSAGE];
    }
} else {

    // IF empty data is received
    $response[KEY_SUCCESS] = ERROR_EMPTY;
    $response[KEY_MESSAGE] = ERROR_EMPTY_MESSAGE;
}

echo json_encode($response, JSON_FORCE_OBJECT);
?>