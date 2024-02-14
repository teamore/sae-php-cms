<?php
namespace App\Model;
use App\Database;
use App\Traits\Softdeletable;
use App\Traits\Timestampable;
use App\Traits\MediaAttachable;
class Post extends AbstractModel {
    use Timestampable, Softdeletable, MediaAttachable;
    protected $id;
    protected $title;
    protected $content;
    protected $author;
    protected static $table = 'posts';

    public static function all(?array $criteria = null, ?int $limit = null, ?int $offset = null) {
         $sql = "
            SELECT p.*, u.username FROM `".self::getTable()."` p 
            LEFT JOIN `".User::getTable()."` u ON u.`id`=p.`user`
            ". static::where($criteria)
            . (isset($limit) ? "LIMIT $limit " : "") 
            . (isset($offset) ? "OFFSET $offset " : "") . "
         ;";
         # retrieve results
         $results = Database::connect()->query($sql)
             ->fetchAll(\PDO::FETCH_ASSOC);
        
        foreach ($results as &$result) {
            $result['media'] = !empty($result['media']) ? json_decode($result['media']) : null;
        }
        return $results;
    }

    public static function find($id) {
        # retrieve results
        return Database::connect()->query("
            SELECT p.*, u.`username`, u.`email`, COUNT(l.`id`) AS `likes` FROM `".self::getTable()."` p 
            LEFT JOIN
                `".User::getTable()."` u
            ON p.`user` = u.`id`
            LEFT JOIN
                `post_likes` l
            ON l.`post` = p.`id`
            WHERE p.`id`='$id'
            GROUP BY l.`post`
            ;
            ")->fetch(\PDO::FETCH_ASSOC);        
    }
    public static function update($data) {
        return Database::connect()->query("
            UPDATE `".self::$table."` SET 
            `title`='$data[title]',
            `author`='$data[author]',
            `content`='$data[content]',
            `updated_at`='".date('Y-m-d H:i:s')."'
            WHERE 
            `id`='$data[id]' AND
            `user`='$data[user]'
            ;
        ");     
    }
    public static function insert($data) {
        $sql = "INSERT INTO `".self::$table."` (
            `title`, 
            `user`,
            `author`,
            `content`,
            `created_at`,
            `updated_at`
            ) VALUES (
                '$data[title]',
                '$data[user]',
                '$data[author]',
                '$data[content]',
                '".date('Y-m-d H:i:s')."',
                '".date('Y-m-d H:i:s')."'

            );";
        $pdo = Database::connect();
        $pdo->query($sql); 
        return $pdo->lastInsertId();
    }
}