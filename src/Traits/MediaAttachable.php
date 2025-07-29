<?php
namespace App\Traits;
use App\Database;
trait MediaAttachable {
    public static function attachMedia($media, $id) {
        $table = static::$table;
        $sql = "UPDATE `$table` SET `media`='".json_encode($media)."' WHERE `id`='$id'";
        Database::connect()->exec($sql);        
    }
    public static function addFileToMedia($mediaFile, $id, $mediaId) {
        $table = static::$table;
        $db = Database::connect();
        $sql = "SELECT `media` FROM `$table` WHERE `id`='$id';";
        $media = $db->query($sql)->fetchColumn();
        $media = json_decode($media);
        array_splice($media, $mediaId, 1, $mediaFile['media']);
        $sql = "UPDATE `$table` SET `media`='".json_encode($media)."' WHERE `id`='$id'";
        $db->exec($sql);          
    }

}