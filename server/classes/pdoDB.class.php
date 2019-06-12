<?php
require_once('ApplicationRegistry.class.php');
/**
 * Created by PhpStorm.
 * User: w15022250
 * Date: 18/10/2017
 * Time: 09:26
 */
class pdoDB {
    private static $dbConnection = null;

    // make the next 2 functions private to prevent normal
    // class instantiation
    private function __construct() {
    }
    private function __clone() {
    }

    /**
     * Return DB connection or create initial connection
     * @param string $dns
     * @return object (PDO)
     * @access public
     */
    public static function getConnection() {
        // if there isn't a connection already then create one
        if ( !self::$dbConnection ) {
            try {
                // connection options to include using exception mode
                $options = array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                );
                $dns = ApplicationRegistry::getDNS();
                $username = ApplicationRegistry::getUsername();
                $password = ApplicationRegistry::getPassword();
                // pass in the options as the last parameter so pdo uses exceptions
                self::$dbConnection = new PDO($dns , $username, $password, $options );
            }
            catch( PDOException $e ) {
                // in a production system you would log the error not display it
                echo $e->getMessage();
            }
        }
        // return the connection
        return self::$dbConnection;
    }

}