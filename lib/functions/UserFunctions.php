<?php

/*
 * This file contains all the necessesary functions to administrate the user database.
 */
require_once 'MySql.php';
require_once 'User.php';
require_once 'generalFunctions.php';

$_connection = connectMySql();

function removeUser($username) {
    if (!userExists($username)) {
        throw new IllegalArgumentException("Requested user does not exist (or database-error.)");
    }

    $userID = getUser($username)->__get("userID");
    $connection = $GLOBALS["_connection"];
    $sql = "DELETE FROM UserData WHERE ID = $userID ";
    $result = mysql_query($sql);
    if (!$result) {
        throw new SqlException(mysql_errno($connection) . ": " . mysql_error($connection) . "\n");
    }
    return userExists($username); //retruns if the remove was a sucess
}

function confirm_user_reg($activation_key) {

    $connection = $GLOBALS["_connection"];

    $activation_key = mysql_real_escape_string($activation_key);

    //tries to get the user for the given activation key
    $sql = "SELECT Username FROM UserData WHERE activationKey = '$activation_key';";
    $result = mysql_query($sql);

    if (!$result) {
        throw new SqlException(mysql_errno($connection) . ": " . mysql_error($connection) . "\n");
    }

    $userdataarray = mysql_fetch_array($result);
    try {
        $return = getUser($userdataarray["Username"]);
    } catch (IllegalArgumentException $exc) {
        $return = false;
    }

    //only if there is an (inactive) user for the given activation key it changes the data in the database
    if (($return != false) && ($return->__get('activeUser') != 1)) {
        $sql = "UPDATE UserData SET activeUser =  1 WHERE activationKey = '$activation_key';";
        if ($return) {
            updateLastAction($return);
        }
        $result = mysql_query($sql);
        if (!$result) {
            throw new SqlException(mysql_errno($connection) . ": " . mysql_error($connection) . "\n");
        }
    } else {
        $return = false;
    }

    //returns the user or false if there was any problem like no user for the key or already activaded key
    return $return;
}

function isActiveUser($username) {
    if (getUser($username)->__get('activeUser') == 1) {
        return true;
    } else {
        return false;
    }
}

function changeUserMail($username, $newEmail) {
    $userID = getUser($username)->__get("userID");

    $connection = $GLOBALS["_connection"];
    $sql = "UPDATE UserData SET Email =  '$newEmail' WHERE ID = $userID;";
    $result = mysql_query($sql);
    if (!$result) {
        throw new SqlException(mysql_errno($connection) . ": " . mysql_error($connection) . "\n");
    }
    return getUser($username);
}

function deactivateUser($username) {
    $userID = getUser($username)->__get("userID");

    $connection = $GLOBALS["_connection"];
    $sql = "UPDATE UserData SET activeUser =  '0' WHERE ID = $userID;";
    $result = mysql_query($sql);
    if (!$result) {
        throw new SqlException(mysql_errno($connection) . ": " . mysql_error($connection) . "\n");
    }
    return getUser($username);
}

function activateUser($username) {
    $userID = getUser($username)->__get("userID");

    $connection = $GLOBALS["_connection"];
    $sql = "UPDATE UserData SET activeUser =  '1' WHERE ID = $userID;";
    $result = mysql_query($sql);
    if (!$result) {
        throw new SqlException(mysql_errno($connection) . ": " . mysql_error($connection) . "\n");
    }
    return getUser($username);
}

function isLoggedIn() {
    if (!isset($_COOKIE['userID']) || !isset($_COOKIE['PHPSESSID'])) {
        return false;
    } else {
        session_start();
        if (($_SESSION['userID'] != $_COOKIE['userID']) || ($_SESSION['REMOTE_ADDR'] != $_SERVER['REMOTE_ADDR']) || ($_SESSION['HTTP_USER_AGENT'] != $_SERVER['HTTP_USER_AGENT'])) {
            session_write_close();
            return false;
        } else {
            if (userExists($_SESSION['username'])) {
                $user = getUser($_SESSION['username']);
                session_write_close();
                return $user;
            } else {
                return false;
            }
        }
    }
}

function login($Username, $Password) {
    if (!userExists($Username)) {
        echo "Es wurde kein Benutzer mit dem angegebenen Namen gefunden.";
    } elseif (!isActiveUser($Username)) {
        echo "Der Benutzer ist deaktiviert. Bitte checken deine Mails um das benutzerkonto zu aktivieren.";
    } else {
        $user = getUser($Username);
        $salt = 's-)_a*';
        $_password = sha1($Password . $salt);
        if (!($user->__get("password") == $_password)) {
            echo "Bitte geben Sie das richtige Password ein.";
        } else {
            if (isLoggedIn()) {
                echo "Sie sind bereits eingeloggt.";
            } else {
                echo "Das pw war richtig, sie sind nun eingeloggt.";
                session_start();
                updateLastAction($user);
                $_SESSION['userID'] = $user->__get("userID");
                $_SESSION['username'] = $user->__get("username");
                $_SESSION['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
                $_SESSION['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
                setcookie('userID', $user->__get("userID"), time() + (60 * 60), '/'); // eine Stunde
                $_COOKIE['userID'] = $user->__get("userID");
                session_write_close();
                return $user;
            }
        }
    }
}

function updateLastAction(&$user) {
    $user->setLastAction();

    $userID = $user->__get("userID");

    $connection = $GLOBALS["_connection"];
    $sql = "UPDATE UserData SET lastAction =  '" . time() . "' WHERE ID = $userID;";
    $result = mysql_query($sql);
    if (!$result) {
        throw new SqlException(mysql_errno($connection) . ": " . mysql_error($connection) . "\n");
    }
}

function logout() {
    if (!isLoggedIn()) {
        return false; //there is no user to logout
    } else {
        session_start();
        setcookie('userID', '', strtotime('-1 day'), '/');
        unset($_COOKIE['userID']);
        setcookie('PHPSESSID', '', strtotime('-1 day'), '/');
        unset($_COOKIE['PHPSESSID']);
        session_destroy();
        return true; //successfully logged out
    }
}

function saveUser($username, $password, $Email) {
    $connection = $GLOBALS["_connection"];

    //Encrypt password for database
    $salt = 's-)_a*';
    $password = sha1($password . $salt);
    $rand_str = random_string('alnum', 8);
    $activation_key = sha1($rand_str . $salt);

    $sql = "INSERT INTO `UserData`
        (`ID` , `Username` , `Password` , `Email` , `activeUser` , `lastAction`, `activationKey`, `regTime`)
        VALUES
        (NULL , '$username', '$password', '$Email', 0, " . time() . ", '$activation_key', " . time() . ");";

    $result = mysql_query($sql);
    if (!$result) {
        throw new SqlException(mysql_errno($connection) . ": " . mysql_error($connection) . "\n");
    }

    //build email to be sent
    $sql = "SELECT * FROM MailSamples WHERE MailID = 1 LIMIT 1";
    $result = mysql_query($sql);

    if (!$result) {
        throw new SqlException(mysql_errno($connection) . ": " . mysql_error($connection) . "\n");
    }

    $mailData = mysql_fetch_array($result);
    //TODO in $message die Variablen mit str_replace ersetzen
    $message = $mailData['Message'];
    $message = str_replace('.$site_url.', '81.169.154.162/PhpProject1', $message);
    $message = str_replace('.$user.', "$username", $message);
    $message = str_replace('.$activation_key.', "$activation_key", $message);
    $subject = $mailData["Subject"];
    // To send HTML mail, the Content-type header must be set
    $headers = $mailData["Headers"] . "Content-type: text/html; charset=iso-8859-1\r\n";
    mail($Email, $subject, $message, $headers);

//
    return getUser($username);
}

function changeUserPw($username, $newPwHash) {
    //TODO
}

/*
 * Retruns the user with the given username from database. if the user does not exist, it throws an IllegalArgumentException.
 */

function getUser($username) {

    if (!userExists($username)) {
        throw new IllegalArgumentException("Requested user does not exist (or database-error.)");
    } else {
        $connection = $GLOBALS["_connection"];

        $sql = "SELECT * FROM UserData WHERE Username = '$username' LIMIT 1";
        $result = mysql_query($sql);

        if (!$result) {
            throw new SqlException(mysql_errno($connection) . ": " . mysql_error($connection) . "\n");
        }

        $userdataarray = mysql_fetch_array($result);
        $user = new User($userdataarray["Username"], $userdataarray["Password"], $userdataarray["Email"], $userdataarray["ID"], $userdataarray["activeUser"], $userdataarray["lastAction"]);

        return $user;
    }
}

/*
 * Returns whether the user with the given username exists in the database.
 */

function userExists($username) {
    $connection = $GLOBALS["_connection"];

    $sql = "SELECT ID FROM UserData WHERE Username = '$username' LIMIT 1;";
    $result = mysql_query($sql);
    if (mysql_num_rows($result) == 1) {
        return true;
    } else {
        return false;
    }
}

?>