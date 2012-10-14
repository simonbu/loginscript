<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

$new_include_path = '../lib/connections/:../lib/functions/:../lib/Exceptions/:../lib/objects/';
set_include_path($new_include_path);
require_once 'UserFunctions.php';



if ('POST' == $_SERVER['REQUEST_METHOD']) {
    if (!isset($_POST['formaction'])) {
        echo 'Benutzen sie nur Formulare von der Homepage.';
    }
}

$logout = logout();
if ($logout) {
    echo 'Sie sind nun ausgeloggt.';
} else {
    echo "Sie müssen eingeloggt sein um diese Funktion nutzen zu können.";
}

?>
