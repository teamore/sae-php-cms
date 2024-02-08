<?php

namespace App\Controller;


class DefaultController extends AbstractController
{
    protected String $appRoot = '/var/www/html/public/';
    protected String $uploadPath = '/var/www/html/public/assets/uploads/';
    protected String $uploadPublicPath = '/assets/uploads/';
    public function index()
    {
        # retrieve results
        $results = $this->getDbConnection()->query("SELECT * FROM `posts`;")->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($results as &$result) {
            $result['media'] = !empty($result['media']) ? json_decode($result['media']) : null;
        }
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
        $result['media'] = json_decode($result['media']);   
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
        $media = $this->handleFileUploads("posts/$data[id]/");

        # Store media array as JSON
        if (count($media) > 0) {
            $sql = "UPDATE `posts` SET `media`='".json_encode($media)."' WHERE `id`='$data[id]'";
            $this->getDbConnection()->exec($sql);    
        }
        return $result;
    }
    public function handleFileUploads($path):Array {
        $media = [];
        foreach($_FILES as $file) {
            if ($file['error']) {
                $this->addMessage(["code"=>422, "message"=>"File ".$file['name']." could not be uploaded. (error $file[error])"]);
            }
            if ($file['tmp_name'] && !$file['error']) {
                $fullPath = $this->uploadPath . $path;

                # Create Directory
                if (!file_exists($fullPath)) {
                    mkdir($fullPath, 0700, true);
                }

                # Generate unique filename
                $file['target'] = $this->generateUniqueFilename($fullPath);
                
                # Generate and store thumbnail
                $file['thumb'] = 't_' . $file['target'];
                $this->generateImage($file['tmp_name'], $fullPath . $file['thumb'], 100, 100);
                
                # Move temporary file to final destination
                $fileDestination = $fullPath . $file['target'];
                if (!file_exists($fileDestination)) {
                    move_uploaded_file($file['tmp_name'], $fileDestination);
                }

                # Create and append entry to $media array
                $media[] = [
                    'path'=>$path.$file['target'], 
                    'type'=> $file['type'],
                    'thumb'=>$path . $file['thumb'],
                    'size'=>filesize($fileDestination), 
                    'original'=>$file['name']
                ];           
            }
        }
        return $media;        
    }
    public function generateUniqueFilename($path, $prefix = '') {
        $uniqueName = tempnam($path, $prefix);
        unlink($uniqueName);
        return substr($uniqueName, strlen($path));
    }
    public function generateImage(String $source, String $targetFilename, int $width, int $height, int $quality = 75) {
        $targetImage = imagecreatetruecolor($width, $height);
        $sourceImage = imagecreatefromjpeg($source);
        list($w, $h) = getimagesize($source);
        $ratio = $w / $h;
        $newRatio = $width / $height;
        imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, $width, $height, $w, $h);
        return imagejpeg($targetImage, $targetFilename, $quality);
    }
    public function postKill() {
        $id = $this->query['post_id'];
        $uid = $this->getUserId(true);
        $media = $this->getDbConnection()->query("SELECT `media` FROM `posts` WHERE `id`='$id';")->fetchColumn();
        $media = json_decode($media, true);
        foreach($media as $file) {
            unlink("/var/www/html/public/".$file['path'].$file['name']);
            unlink("/var/www/html/public/".$file['thumb']);
        }
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
