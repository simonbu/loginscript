<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

$new_include_path = '../lib/connections/:../lib/functions/:../lib/Exceptions/:../lib/objects/';
set_include_path($new_include_path);
require_once 'UserFunctions.php';

$invalidform = false;
//if the site was requestet by post-Method (direct requests (get-Method) won't be valid)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // now we check if every form-element was used
    if ((count($_POST) != 7) || (!isset($_POST['Username'], $_POST['Password'], $_POST['captcha'], $_POST['PasswordConfirmation'], $_POST['Email'], $_POST['Antwort'], $_POST['formaction3']))) {
        echo 'Benutzen sie nur Formulare von der Homepage.';
        $invalidform = true;
    }

    //checks if both passwords are equal
    if ($_POST['Password'] != $_POST['PasswordConfirmation']) {
        echo 'Bitte geben sie das gleiche Password ein.';
        $invalidform = true;
    }

    //checks if every field is filled out
    if (($Username = trim($_POST['Username'])) == '' OR
            ($Password = trim($_POST['Password'])) == '' OR
            ($PasswordConfirmation = trim($_POST['PasswordConfirmation'])) == '' OR
            ($Email = trim($_POST['Email'])) == '' OR
            ($Answer = trim($_POST['Antwort'])) == '') {
        echo 'Bitte füllen sie das Formular vollständig aus.';
        $invalidform = true;
    }

    //Captcha check
    if (md5($Answer) != $_POST['captcha']) {
        echo 'Bitte geben sie die richtige Antwort an.';
        $invalidform = true;
    }

    //regular expression checks for nonsense names
    if (!preg_match('~\A\S{3,30}\z~', $Username)) {
        echo 'Der Benutzername darf nur aus 3 bis 30 Zeichen bestehen und ' .
        'keine Leerzeichen enthalten.';
        $invalidform = true;
    }

    if ($invalidform) {
        die();
    }

    if (!userExists($Username)) {
        $user = saveUser($Username, $Password, $Email);
        echo $user->__get("username") . " wurde erfolgreich hinzugefügt";
    } else {
        echo "Der Benutzername wird bereits verwendet.";
    }
} else {
    echo "Bitte verwenden Sie das register Form.";
}
?>