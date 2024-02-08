<?php
namespace App\Model;
use App\Database;
class AbstractModel {
    protected static $table;
    public static function findBy($condition, $fields = '*') {
        $table = static::$table;
        return Database::connect()->query("SELECT $fields FROM `$table` WHERE $condition;")->fetch(\PDO::FETCH_ASSOC);
    }
    public function db() {
        $pdo = Database::connect();
        return $pdo;
    }
}