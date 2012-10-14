<?php

/**
 * Description of User
 *
 * @author Simon Buchholz
 * @version 0.1
 */

class User {

    private $_username;
    private $_password;
    private $_Email;
    private $_userID;
    private $_activeUser;
    private $_lastAction;

    public function __construct($username, $password, $Email, $userID, $activeUser, $lastAction) {
        $this->_username = $username;
        $this->_password = $password;
        $this->_Email = $Email;
        
        if (isset($userID) && $userID >= 0) {
            $this->_userID = $userID;
        } else {
            $this->_userID = -1;
        }
        
        if (isset($activeUser)) {
            $this->_activeUser = $activeUser;
        } else {
            $this->_activeUser = -1;
        }
        
        if (isset($lastAction) && ($lastAction >= 0)) {
            $this->_lastAction = $lastAction;
        } else {
            $this->_lastAction = -1;
        }
    }
    
    public function setLastAction() {
        $this->_lastAction = time();
    }

    public function __get($value) {
        switch ($value) {
            case "username":
                return $this->_username;
                break;
            case "userID":
                return $this->_userID;
                break;
            case "password":
                return $this->_password;
                break;
            case "Email":
                return $this->_Email;
                break;
            case "activeUser":
                return $this->_activeUser;
                break;
            case "lastAction":
                return $this->_lastAction;
                break;
            default:
                throw new Exception("No such attribute.");
        }
    }
    
    public function createCountdown() {
        $countdown = new Countdown();
        $countdown->saveCountdown();
    }
    
    

}

?>
