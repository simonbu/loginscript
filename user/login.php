<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

$new_include_path = '../lib/connections/:../lib/functions/:../lib/Exceptions/:../lib/objects/';
set_include_path($new_include_path);
require_once 'UserFunctions.php';


$invalidform = false;

if ('POST' == $_SERVER['REQUEST_METHOD']) {

    if (!isset($_POST['Username'], $_POST['Password'], $_POST['formaction2'])) {
        echo 'Benutzen sie nur Formulare von der Homepage.';
        $invalidform = true;
    }

    if (('' == $Username = trim($_POST['Username'])) OR
            ('' == $Password = trim($_POST['Password']))) {
        echo 'Bitte füllen sie das Formular vollständig aus.';
        $invalidform = true;
    }
} else {
    echo "Bitte verwenden Sie das login Form.";
    $invalidform = true;
}

login($Username, $Password);


?>
