<?php
namespace App\Model;
use App\Database;
class PostComment extends AbstractModel {
    protected static $table = "post_comments";
    public static function insert($postId, $userId, $content, $title = '') {
        return Database::connect()->exec("
            INSERT INTO `post_comments` (`user`, `post`, `title`, `content`, `created_at`, `updated_at`) 
            VALUES 
            ('$userId', '$postId', '$title', '$content', '".date('Y-m-d H:i:s')."', '".date('Y-m-d H:i:s')."');
        ");            
    }
}