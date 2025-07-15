<?php

namespace App\Controller;


class DefaultController extends AbstractController
{
    
    public function index()
    {
        # retrieve results
        $results = $this->getDbConnection()->query("SELECT * FROM `posts`;")->fetchAll(\PDO::FETCH_ASSOC);
        $this->setView('index.html', ['results' => $results]);
    }

    public function postShow() {
        $id = $this->query['post_id'];
        $uid = $this->getUserId();

        # retrieve results
        $result = $this->getDbConnection()->query("
            SELECT p.*, u.`username`, u.`email`, COUNT(l.`id`) AS `likes` FROM `posts` p 
            LEFT JOIN
                `users` u
            ON p.`user` = u.`id`
            LEFT JOIN
                `post_likes` l
            ON l.`post` = p.`id`
            WHERE p.`id`='$id'
            GROUP BY l.`post`
            
            ;
            ")->fetch(\PDO::FETCH_ASSOC);

        if (!$result) {
            throw new \Exception("This Resource does not exist", 404);
        }
        $result['media'] = json_decode($result['media'] ?? "");   
        if ($uid) {
            $result2 = $this->getDbConnection()->query("
            SELECT `id` FROM `post_likes` WHERE `post`='$id' AND `user`='$uid';
            ")->fetch(\PDO::FETCH_ASSOC);
            $result['ilike'] = ($result2 !== false);
        }            
        # call view
        $this->setView('post.html', ['result' => $result]);
    }

    public function postEdit() {
        $id = $this->query['post_id'] ?? 0;
        if ($id) {
            # retrieve results
            $result = $this->getDbConnection()->query("SELECT * FROM `posts` WHERE `id`='$id';")->fetch(\PDO::FETCH_ASSOC);
            if ($result) {
                if ($result['user'] !== $this->getUserId()) {
                    throw new \Exception('Users are not allowed to edit foreign Posts', 403);
                }
            } else {
                throw new \Exception('This Post does not exist', 404);
            }
        }
        $this->setView('post_update.html', ['result' => $result ?? []]);        
    }
    public function postSave() {
        $data = $_REQUEST;

        # TODO: perform mysql query sanitation
        $user = $this->getUser();
        if (!$user) {
            throw new \Exception('Only authenticated Users may create Posts', 401);
        }
        if ($data['id']) {
            if (!is_numeric($data['id'])) {
                return;
            }
            $result = $this->getDbConnection()->query("
                UPDATE `posts` SET 
                `title`='$data[title]',
                `author`='$data[author]',
                `content`='$data[content]',
                `updated_at`='".date('Y-m-d H:i:s')."'
                WHERE 
                `id`='$data[id]' AND
                `user`='$user->id'
                ;
                ");     
        } else {
            $sql = "INSERT INTO `posts` (
                `title`, 
                `user`,
                `author`,
                `content`,
                `created_at`,
                `updated_at`
                ) VALUES (
                    '$data[title]',
                    '$user->id',
                    '$data[author]',
                    '$data[content]',
                    '".date('Y-m-d H:i:s')."',
                    '".date('Y-m-d H:i:s')."'

                );";
            $conn = $this->getDbConnection();
            $result = $conn->query($sql); 
            $data['id'] = $conn->lastInsertId();
        }
        $media = [];
        $files = 0;
        foreach($_FILES as $file) {
            if ($file['error']) {
                $this->addMessage(["code"=>422, "message"=>"File ".$file['name']." could not be uploaded."]);
            }
            if ($file['tmp_name'] && !$file['error']) {
                $path = "assets/uploads/posts/$data[id]/";
                $fullPath = "/var/www/html/public/$path";
                if (!file_exists($fullPath)) {
                    mkdir($fullPath, 0777, true);
                }
                $fileDestination = $fullPath .$file['name'];
                if (!file_exists($fileDestination)) {
                    move_uploaded_file($file['tmp_name'], $fileDestination);
                }
                $media[] = ['path'=>$path, 'name'=>$file['name'], 'size'=>filesize($fileDestination), 'original'=>$file['name']];           
                $files++;
            }
        }
        if ($files > 0) {
            $sql = "UPDATE `posts` SET `media`='".json_encode($media)."' WHERE `id`='$data[id]' AND `user`='$user->id'";
            $this->getDbConnection()->exec($sql);    
        }
        return $result;
    }
    public function postKill() {
        $id = $this->query['post_id'];
        $uid = $this->getUserId(true);
        $result = $this->getDbConnection()->exec("DELETE FROM `posts` WHERE `id`='$id' AND `user`='$uid' LIMIT 1;");
        return $this->index();
    }

    public function postLikeSave() {
        $postId = $this->query['post_id'];
        $uid = $this->getUserId(true);

        try {
            $result = $this->getDbConnection()->query("SELECT `user` FROM `posts` WHERE `id`='$postId'")->fetchColumn();
            if ($uid === $result) {
                throw new \Exception('Users must not like their own posts.', 400);
            }
            $result = $this->getDbConnection()->exec("INSERT INTO `post_likes` 
            (`post`,`user`,`created_at`) VALUES 
            ('$postId', '$uid', '".date('Y-m-d H:i:s')."');
            ");            
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage(),304);
        }
        return ["affectedRows" => $result];
    }

    public function postLikeDelete() {
        $postId = $this->query['post_id'];
        $uid = $this->getUserId(true);

        try {
            $result = $this->getDbConnection()->exec("DELETE FROM `post_likes` WHERE `post`=$postId AND `user`=$uid;");
        } catch (\PDOException $e) {
            throw new \Exception($e->getMessage(),500);
        }
        if (!$result) {
            throw new \Exception("This resource has already been deleted.",304);
        }
        return ["affectedRows" => $result];
    }


}
