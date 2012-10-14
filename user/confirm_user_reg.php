<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        error_reporting(E_ALL);
        ini_set('display_errors', '1');

        $new_include_path = '../lib/connections/:../lib/functions/:../lib/Exceptions/:../lib/objects/';
        set_include_path($new_include_path);
        include 'UserFunctions.php';

        if (isset($_GET["activation_key"])) {
            $activation_key = $_GET["activation_key"];

            $result = confirm_user_reg("$activation_key");
            if ($result == false) {
                echo "Ein Fehler ist aufgetreten, bitte kontaktieren Sie den Systemadministratior.";
            } else {
                echo "Das Benutzerkonto von " . $result->__get("username") . " wurde erfolgreich aktiviert";
            }
        } else {
            echo "Ein Fehler ist aufgetreten, bitte kontaktieren Sie den Systemadministratior.";
        }
        ?>
    </body>
</html>
