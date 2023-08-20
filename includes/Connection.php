<?php
class ConnectionHelper
{
    private static $connection;
    public static function getConnection()
    {
        if (self::$connection == null) {
            self::$connection = new PDO('mysql:host=localhost;dbname=digitalkirana', 'root', '');
            // self::$connection = new PDO('mysql:host=s782.bom1.mysecurecloudhost.com;dbname=balajicl_digitalkirana', 'balajicl_saileshrijal', 'Manager@0011');
        }
        return self::$connection;
    }
}
