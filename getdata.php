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
    $_SESSION[KEY_USERID] = $data[GET_KEY_SEPARATOR . KEY_USERID];

    // Get last access ID and put it in Session variable
    $_SESSION[KEY_USER_LAID] = $user->get_laid();

    // Verify the user
    if ($user->verify_token($data[GET_KEY_SEPARATOR . KEY_TOKEN])) {

        // Put new token into response array
        $response[KEY_TOKEN] = $_SESSION[KEY_TOKEN];

        // Get the action
        $action = $data[GET_KEY_SEPARATOR . GET_KEY_ACTION];

        // Perform switch action
        switch ($action) {

            // FOR KEYS
            case GET_ACTION_VALUE_KEYS:

                $keys = array();
                $tempdata = $user->get_all_keys();
                for ($x = 0; $x < count($tempdata); $x++) {
                    $keys[$x] = array();
                    $keys[$x][X_KEY . KEY_X_ID] = $tempdata[$x][X_KEY . DB_X_ID];
                    $keys[$x][X_KEY . KEY_X_NAME] = $tempdata[$x][X_KEY . DB_X_Name];
                    $keys[$x][X_KEY . KEY_X_INFO] = $tempdata[$x][X_KEY . DB_X_INFO];
                    $keys[$x][X_KEY . KEY_X_CDate] = $tempdata[$x][X_KEY . DB_X_CDATE];
                }
                $response[POST_KEY_ALL_KEYS] = $keys;

                $response[KEY_SUCCESS] = ERROR_NIL;
                $response[KEY_MESSAGE] = "Successfully sent the data!";
                break;

            // FOR Secrets
            case GET_ACTION_VALUE_SECRETS:

                $secrets = array();
                $tempdata = $user->get_all_secrets();
                for ($x = 0; $x < count($tempdata); $x++) {
                    $secrets[$x] = array();
                    $secrets[$x][X_SECRET . KEY_X_ID] = $tempdata[$x][X_SECRET . DB_X_ID];
                    $secrets[$x][X_SECRET . KEY_X_NAME] = $tempdata[$x][X_SECRET . DB_X_Name];
                    $secrets[$x][X_SECRET . KEY_X_INFO] = $tempdata[$x][X_SECRET . DB_X_INFO];
                    $secrets[$x][X_SECRET . KEY_X_Add] = $tempdata[$x][X_SECRET . DB_X_ADD];
                    $secrets[$x][X_SECRET . KEY_X_CDate] = $tempdata[$x][X_SECRET . DB_X_CDATE];
                    $secrets[$x][X_SECRET . KEY_X_Data] = $tempdata[$x][X_SECRET . DB_X_DATA];
                }
                $response[POST_KEY_ALL_SECRETS] = $secrets;

                $response[KEY_SUCCESS] = ERROR_NIL;
                $response[KEY_MESSAGE] = "Successfully sent the data!";
                break;

            case GET_ACTION_VALUE_BOTH:
                //Secrets
                $secrets = array();
                $sec_tempdata = $user->get_all_secrets();
                for ($x = 0; $x < count($sec_tempdata); $x++) {
                    $secrets[$x] = array();
                    $secrets[$x][X_SECRET . KEY_X_ID] = $sec_tempdata[$x][X_SECRET . DB_X_ID];
                    $secrets[$x][X_SECRET . KEY_X_NAME] = $sec_tempdata[$x][X_SECRET . DB_X_Name];
                    $secrets[$x][X_SECRET . KEY_X_INFO] = $sec_tempdata[$x][X_SECRET . DB_X_INFO];
                    $secrets[$x][X_SECRET . KEY_X_Add] = $sec_tempdata[$x][X_SECRET . DB_X_ADD];
                    $secrets[$x][X_SECRET . KEY_X_CDate] = $sec_tempdata[$x][X_SECRET . DB_X_CDATE];
                    $secrets[$x][X_SECRET . KEY_X_Data] = $sec_tempdata[$x][X_SECRET . DB_X_DATA];
                }
                $response[POST_KEY_ALL_SECRETS] = $secrets;

                //Keys

                $keys = array();
                $key_tempdata = $user->get_all_keys();
                for ($x = 0; $x < count($key_tempdata); $x++) {
                    $keys[$x] = array();
                    $keys[$x][X_KEY . KEY_X_ID] = $key_tempdata[$x][X_KEY . DB_X_ID];
                    $keys[$x][X_KEY . KEY_X_NAME] = $key_tempdata[$x][X_KEY . DB_X_Name];
                    $keys[$x][X_KEY . KEY_X_INFO] = $key_tempdata[$x][X_KEY . DB_X_INFO];
                    $keys[$x][X_KEY . KEY_X_CDate] = $key_tempdata[$x][X_KEY . DB_X_CDATE];
                }
                $response[POST_KEY_ALL_KEYS] = $keys;

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