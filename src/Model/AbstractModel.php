<?php
namespace App\Model;
use App\Database;
class AbstractModel {
    protected static $table;
    public static function findBy($condition, $fields = '*') {
        return Database::connect()->query("SELECT $fields FROM `".static::$table."` WHERE $condition;")->fetch(\PDO::FETCH_ASSOC);
    }
    public static function getTable(): string {
        return static::$table;
    }
    public static function count(?array $criteria = null): ?int {
        $sql = "SELECT COUNT(*) FROM `".static::$table."` ".static::where($criteria);
        return Database::connect()->query($sql.";")->fetchColumn();
    }
    public static function where(?array $criteria = null):string {
        $sql = " WHERE 1 ";
        if (isset($criteria)) {
            foreach($criteria as $field => $value) {
                $sql .= " AND `$field`='$value'";
            }
        }
        return $sql;
    }
    public function db() {
        $pdo = Database::connect();
        return $pdo;
    }
}