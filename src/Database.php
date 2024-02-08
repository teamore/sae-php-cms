<?php
namespace App;
class Database {
    public static function connect() {
        # database connection parameters
        $host = getenv('MYSQL_HOST') ?: 'sae-php-cms-mysql';
        $database = getenv('MYSQL_DATABASE');
        $username = getenv('MYSQL_USER');
        $password = getenv('MYSQL_PASSWORD');

        # establish database connection
        $pdo = new \PDO("mysql:host=$host;dbname=$database", $username, $password);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }    
}