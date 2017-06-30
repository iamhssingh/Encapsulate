<?php
//set timezone
require_once 'globalvars.php';
date_default_timezone_set("Asia/Kolkata");

ob_start();
session_start();

//database credentials
define('DBHOST','localhost');
define('DBUSER','root');
define('DBPASS','admin_alpha');
define('DBNAME','secretword');

//application address
define('DIR','http://local.encapsulate.com');
define('SITEEMAIL','admin@ities.xyz');

try {
	//create PDO connection 
	$db = new PDO("mysql:host=".DBHOST.";dbname=".DBNAME, DBUSER, DBPASS);
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch(PDOException $e) {
	//show error
    echo '<p class="bg-danger">'.$e->getMessage().'</p>';
    exit;
}

//include the user class, pass in the database connection
include($_SERVER['DOCUMENT_ROOT'].'/encapsulate/classes/user.php');

$user = new User($db);
?> 