<?php

require_once 'Exceptions.php';

function connectMySql() {
    
    
    
    $_dbServer = 'localhost';
    $_dbPassword = 'password';
    $_dbUser = 'username';
    $_dbName = 'dbname';

    if ($connection = mysql_connect($_dbServer, $_dbUser, $_dbPassword)) {
        $selection = mysql_select_db($_dbName);
        if (!$selection) {
            throw new SqlConnectionException("Wasn't able to select database.");
        }
    } else {
        throw new SqlConnectionException("Wasn't able to connect to MySQL server.");
    }
    return $connection;
    
}

?>
