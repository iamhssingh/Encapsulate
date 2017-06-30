<?php
echo "<PRE>";
print_r($GLOBALS);
echo "<PRE>";

define("KEY_USERID", "userID");

if(count($_POST)>0){
    echo KEY_USERID;
}
$_POST["test"] = null;
if(count($_POST)>0){
    echo "TRUE - 2";
}
echo "<PRE>";
print_r($GLOBALS);
echo "<PRE>";

$_POST["test"] = "abc";
if(count($_POST)>0){
    echo "true - 3";
}
echo "<PRE>";
print_r($GLOBALS);
echo "<PRE>";
