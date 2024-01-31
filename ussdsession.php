<?php
require_once 'Database.php';


class ussdsession {
    
    public $db;
    function __construct() {
        $this->db = new Database("localhost", "root", "", "db");
    }
    
    /**
     * method clearSessions
     * deletes from the session
     * */
    public  function clearSessions($msisdn) {
        $result = $this->db->Alter("DELETE FROM ussd_sessions WHERE msisdn = ?", $msisdn);
        return $result;
    }

    /**
     * Method createSessions
     * creates a new session for the user
     * */
    public  function createSessions( $sessionId, $msisdn, $menu,  $sessionString) {
        $result = $this->db->Alter("INSERT INTO ussd_sessions (sessionId, msisdn, menu,"
                . "ussdString, dateCreated) VALUES (?, ?, ?, ?, NOW())", $sessionId, $msisdn, $menu,  $sessionString);
        return $result;
    }

    /**
     * Method updateSessions 
     * Updates the session string on each request
     * */
    public  function updateSessions($sessionId, $msisdn, $menu, $ussdString) {
        $result = $this->db->Alter("UPDATE ussd_sessions SET menu = ? , ussdString = ?  WHERE msisdn = ? and sessionId = ?", $menu, $ussdString, $msisdn, $sessionId );
        return $result;
    }

    /**
     * Method getSessionString 
     * Updates the session string on each request
     * */
    public  function getSessionString($sessionId, $msisdn) {
        $sessionString = array();
        $result = $this->db->Select("SELECT menu, ussdString FROM ussd_sessions WHERE "
                . "msisdn = ? and sessionId = ?" , $msisdn, $sessionId);

        if (count($result) > 0) {
                $sessionString[0] = $result[0]['menu'];
                $sessionString[1] = $result[0]['ussdString'];
        }
        return $sessionString;
    }

   

}
