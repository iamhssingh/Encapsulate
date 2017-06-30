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
        $response[KEY_TOKEN] = $_SESSION[SESSION_TOKEN];

        // Get the action
        $action = $data[GET_KEY_SEPARATOR . GET_KEY_ACTION];

        // Perform switch action
        switch ($action) {

            case GET_ACTION_VALUE_ADD:

                // Get necessary parameters
                $secname = $data[X_SECRET . GET_KEY_SEPARATOR . KEY_X_NAME];
                $secadd = $data[X_SECRET . GET_KEY_SEPARATOR . KEY_X_Add];
                $secinfo = $data[X_SECRET . GET_KEY_SEPARATOR . KEY_X_INFO];

                // Get Data for Encryption
                $encapID = $data[GET_KEY_SEPARATOR . GET_KEY_ENCRYPTION_ID];
                $keyID = $data[X_KEY . GET_KEY_SEPARATOR . KEY_X_ID];
                $secdata = $data[X_SECRET . GET_KEY_SEPARATOR . KEY_X_Data];

                // Now encrypt
                $sec_encap_data = $user->encap_data($encapID, $keyID, $secdata);

                // Now insert information to database
                if ($user->add_secret($sec_encap_data, $secname, $secinfo, $secadd, $keyID, $encapID)) {
                    $response[KEY_SUCCESS] = ERROR_NIL;
                    $response[KEY_MESSAGE] = "Secret added successfully!";
                } else {
                    $response[KEY_SUCCESS] = $_SESSION[KEY_SUCCESS];
                    $response[KEY_MESSAGE] = $_SESSION[KEY_MESSAGE];
                }
                $response[KEY_SUCCESS] = ERROR_NIL;
                $response[KEY_MESSAGE] = "Successfully sent the data!";
                break;

            // FOR DELETE
            case GET_ACTION_VALUE_REMOVE:
                $secid = $data[X_SECRET . GET_KEY_SEPARATOR . KEY_X_ID];
                if ($user->del_secret($secid)) {
                    $response[KEY_SUCCESS] = ERROR_NIL;
                    $response[KEY_MESSAGE] = "Secret removed successfully!";
                } else {
                    $response[KEY_SUCCESS] = $_SESSION[KEY_SUCCESS];
                    $response[KEY_MESSAGE] = $_SESSION[KEY_MESSAGE];
                }
                $response[KEY_SUCCESS] = ERROR_NIL;
                $response[KEY_MESSAGE] = "Successfully sent the data!";
                break;

            // FOR EDIT
            case GET_ACTION_VALUE_EDIT:

                // Get necessary parameters
                $secname = $data[X_SECRET . GET_KEY_SEPARATOR . KEY_X_NAME];
                $secadd = $data[X_SECRET . GET_KEY_SEPARATOR . KEY_X_Add];
                $secinfo = $data[X_SECRET . GET_KEY_SEPARATOR . KEY_X_INFO];
                $secid = $data[X_SECRET . GET_KEY_SEPARATOR . KEY_X_ID];

                // Get Data for Encryption
                $encapID = $data[GET_KEY_SEPARATOR . GET_KEY_ENCRYPTION_ID];
                $keyID = $data[X_KEY . GET_KEY_SEPARATOR . KEY_X_ID];
                $secdata = $data[X_SECRET . GET_KEY_SEPARATOR . KEY_X_Data];

                // Now encrypt
                $sec_encap_data = $user->encap_data($encapID, $keyID, $secdata);

                if ($user->del_secret($secid) && $user->add_secret($sec_encap_data, $secname, $secinfo, $secadd, $keyID, $encapID)) {
                    $response[KEY_SUCCESS] = ERROR_NIL;
                    $response[KEY_MESSAGE] = "Secret changed successfully!";
                } else {
                    $response[KEY_SUCCESS] = $_SESSION[KEY_SUCCESS];
                    $response[KEY_MESSAGE] = $_SESSION[KEY_MESSAGE];
                }
                $response[KEY_SUCCESS] = ERROR_NIL;
                $response[KEY_MESSAGE] = "Successfully sent the data!";
                break;

            // To show the secret
            case GET_ACTION_VALUE_SHOW:
                $secret = $user->get_secret($data[X_SECRET . GET_KEY_SEPARATOR . KEY_X_ID]);
                if ($data[X_KEY . GET_KEY_SEPARATOR . KEY_X_ID] == (int) $secret[X_KEY . DB_X_ID] && $data[GET_KEY_SEPARATOR . GET_KEY_ENCRYPTION_ID] == (int) $secret['encapID']) {
                    $response[KEY_SUCCESS] = ERROR_NIL;
                    $response[KEY_MESSAGE] = "Decryption successful!";
                    $response[X_SECRET . POST_KEY_X_DATA] = $secret[X_SECRET . DB_X_DATA];
                } else {
                    $response[KEY_SUCCESS] = ERROR_DECRYPT;
                    $response[KEY_MESSAGE] = ERROR_DECRYPT_MESSAGE;
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