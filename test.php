<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

$new_include_path = './lib/connections/:./lib/functions/:./lib/Exceptions/:./lib/objects/';
set_include_path($new_include_path);
include 'UserFunctions.php';

echo "Simon ist aktiv: " . isActiveUser("Simon");

$result = confirm_user_reg("1afc042a9c60bf5ff8588eec3cd3f38ed1fcfac5");
if ($result == false) {
    echo "            Useractivity wurde nicht verändert";
} else {
    echo "           ".$result->__get("username")." erfolgreich aktiviert";
}

//echo "              Jetzt ist Simon ist aktiv: " . isActiveUser("Simon");


 
?>