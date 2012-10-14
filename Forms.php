
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
        <?php
        error_reporting(E_ALL);
        ini_set('display_errors', '1');

        $new_include_path = './lib/connections/:./lib/functions/:./lib/Exceptions/:./lib/objects/';
        set_include_path($new_include_path);
        require_once 'UserFunctions.php';
        ?>
    </head>
    <body>
        <form action="user/logout.php" method="post">
            <fieldset>
                <legend>Ausloggen</legend>
                <p class="info">Um sich auszuloggen klicken sie unten auf den Button.</p>
                <p>
                    <input type="submit" name="formaction" value="Ausloggen" />
                </p>
            </fieldset>
        </form>
        <form action="user/login.php" method="post">
            <fieldset>
                <legend>Einloggen</legend>
                <label>Benutzername:
                    <input type="text" name="Username" />
                </label>
                <label>Password:
                    <input type="password" name="Password" />
                </label>
                <input type="submit" name="formaction2" value="Einloggen" />
            </fieldset>
        </form>
        <p>
            <?php
            // generates captcha
            $a = rand(1, 10);
            $b = rand(1, 10);
            $c = rand(1, 10);
            $result = md5((String) ($a + ($b * $c)));
            $captcha = (string) $a . "+(" . (string) $b . "x" . (string) $c . ")";
            ?>
        </p>
        <form action="user/register.php" method="post">
            <fieldset>
                <legend>Registieren</legend>
                <label>Username:
                    <input type="text" name="Username" />
                </label>
                <label>Password:
                    <input type="password" name="Password" />
                </label>
                <label>Bestätigung:
                    <input type="password" name="PasswordConfirmation" />
                </label>
                <label>Email:
                    <input type="text" name="Email" />
                </label>
                <label><?php print $captcha; ?>:
                    <input type="text" name="Antwort" />
                </label>
                <input type="hidden" name="captcha" value="<?php print $result; ?>" />
                <input type="submit" name="formaction3" value="Registieren" />
            </fieldset>
        </form>
        <p><?php
            if (!isLoggedIn()) {
                echo "Loggen Sie sich ein.         (Wenn Sie die zurück Funktion des Browsers benutzt haben müssen sie die Seite evtl. aktialisieren.)";
            } else {
                echo isLoggedIn()->__get('username') . " ist angemeldet. ";
                echo "Letzte Aktion war " . date('r', isLoggedIn()->__get('lastAction')) . "!         (Wenn Sie die zurück Funktion des Browsers benutzt haben müssen sie die Seite evtl. aktialisieren.)";
            }
            ?></p>
    </body>
</html>
