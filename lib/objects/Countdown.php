<?php

/**
 * Description of Countdown
 *
 * @author Simon Buchholz
 */

class Countdown {
    
    private $_description;
    private $_timestamp;
    private $_owner;
    private $_countdownID = -1;
    
    public function __construct(String $description, int $timestamp, User $owner) {
        $this->_description = $description;
        $this->_timestamp = $timestamp;
        $this->_owner = $owner;
    }
    
    public function __get($value) {
        switch ($value) {
            case "description":
                return $this->_description;
                break;
            case "countdownID":
                return $this->_countdownID;
                break;
            case "timestamp":
                return $this->_timestamp;
                break;
            case "owner":
                return $this->_owner;
                break;
            default:
                throw new Exception("No such attribute.");
        }
    }
    
    public function isOwner(User $user) {
        if ($this->_owner == $user) {
            return true;
        } else {
            return false;
        }
    }

    public function saveCountdown(){
        
    }
}

?>
