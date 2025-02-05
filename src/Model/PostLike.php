<?php
namespace App\Model;
use App\Database;
class PostLike extends AbstractModel {
    protected static $table = "post_likes";
    public static function insert($postId, $userId) {
        return Database::connect()->exec("
            INSERT INTO `post_likes` 
            (`post`,`user`,`created_at`) VALUES 
            ('$postId', '$userId', '".date('Y-m-d H:i:s')."');
        ");            
    }
    public static function unlike($postId, $userId) {
        return Database::connect()->exec("
            DELETE FROM `post_likes` WHERE `post`=$postId AND `user`=$userId;
        ");

    }
}