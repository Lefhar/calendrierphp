<?php

/**
 * @brief  c'est la connexion à la bdd
 * Class Database
 */
class Database
{
    static $host = "localhost";
    static $dbname = "planningv2";
    static $username = "root";
    static $password = "";
    static $db = null;

    static function connect()
    {
        try {
            if (!self::$db) {

                self::$db = new PDO('mysql:host=' . Database::$host . ';dbname=' . Database::$dbname . ';charset=utf8', Database::$username, Database::$password);


            }
        } catch (PDOException $e) {
            print "Erreur de connexion Sql!";
            die();
        }
        return self::$db;
    }
}