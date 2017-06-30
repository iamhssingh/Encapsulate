<?php


// FOR RESPONSE JSON
// GLOBALS
define("KEY_MESSAGE", "message");
define("KEY_SUCCESS","Code");
define("GET_KEY_SEPARATOR","_");

// ERROR CODES
define("ERROR_DATABASE", 500);
define("ERROR_SIGNUP", 400);
define("ERROR_NIL", 200);
define("ERROR_PASSWORD", 401);
define("ERROR_EMPTY", 400);
define("ERROR_TOKEN", 403);
define("ERROR_DECRYPT", 403);

// ERROR MESSAGES
define("ERROR_DATABASE_MESSAGE", "Server Error Occured! Some database error occured.");
define("ERROR_SIGNUP_MESSAGE", "Invalid Request! UserName or EMail ID either malformed or already in use.");
define("ERROR_NIL_MESSAGE", "Success");
define("ERROR_PASSWORD_MESSAGE", "Authentication error! Wrong password or username!");
define("ERROR_EMPTY_MESSAGE", "Invalid Request! No data found.");
define("ERROR_TOKEN_MESSAGE", "Authentication error! Invalid Token used. Clearing local database.");
define("ERROR_DECRYPT_MESSAGE", "Authentication error! Crypted Text will not be converted into Plain text.");

// FOR USER_MASTERS
define("KEY_USERID","userID");
define("KEY_USER_LAID","LAID");
define("KEY_TOKEN","token");
define("KEY_FULLNAME","name");
define("KEY_EMAIL","email");
define("KEY_USERNAME","username");
define("KEY_PASSWORD","password");


// KEY - SECRET ACTIONS
define("GET_KEY_ACTION","action");

// POST DATA
define("POST_KEY_ALL_KEYS" ,"keys");
define ("POST_KEY_ALL_SECRETS" , "secrets");
define ("POST_KEY_X_DATA" ,"data");

// Actions
define("GET_ACTION_VALUE_ADD" ,"add");
define("GET_ACTION_VALUE_EDIT" , "edit");
define("GET_ACTION_VALUE_REMOVE" ,"delete");
define("GET_ACTION_VALUE_SHOW" , "show");

define("GET_ACTION_VALUE_KEYS" , "getkeys");
define ("GET_ACTION_VALUE_SECRETS" , "getsecrets");
define("GET_ACTION_VALUE_BOTH" , "getboth");

// ATTRIBUTES
define ("KEY_X_NAME" , "Name");
define("KEY_X_INFO" , "Info");
define ("KEY_X_ID" , "ID");
define("KEY_X_Data" , "Data");
define("KEY_X_CDate" ,"CDate");
define("KEY_X_Add" ,"Add");

// VALUE of X
define ("X_KEY" , "key");
define("X_SECRET" , "secret");

// For Secret
define("GET_KEY_ENCRYPTION_ID", "encapID");


// Only for Server
define ("DB_X_Name" , "Name");
define("DB_X_INFO" , "Info");
define ("DB_X_ID" , "ID");
define("DB_X_DATA" , "Data");
define("DB_X_CDATE" ,"CDate");
define("DB_X_ADD" ,"Add");

define ("DB_USER_ID" , "userID");
define ("DB_USER_LAID" , "userLAID");

define ("SESSION_ID" , "ID");
define ("SESSION_LAID" , "LAID");
define ("SESSION_TOKEN" , "token");