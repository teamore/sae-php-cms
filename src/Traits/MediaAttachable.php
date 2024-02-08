<?php
namespace App\Traits;
use App\Database;
trait MediaAttachable {
    public static function attachMedia($media, $id) {
        $table = static::$table;
        $sql = "UPDATE `$table` SET `media`='".json_encode($media)."' WHERE `id`='$id'";
        Database::connect()->exec($sql);        
    }

}