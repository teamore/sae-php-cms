<?php

namespace App\Controller;


class DefaultController extends AbstractController
{
    
    public function index()
    {
        # retrieve results
        $results = $this->getDbConnection()->query("SELECT * FROM `posts`;")->fetchAll(\PDO::FETCH_ASSOC);
        $this->display('index.html', ['results' => $results]);
    }

    public function postShow($id) {

        # retrieve results
        $result = $this->getDbConnection()->query("SELECT * FROM `posts` WHERE `id`='$id';")->fetch(\PDO::FETCH_ASSOC);

        # call view
        $this->display('post.html', ['result' => $result]);
    }

    public function postEdit($id) {
        # retrieve results
        $result = $this->getDbConnection()->query("SELECT * FROM `posts` WHERE `id`='$id';")->fetch(\PDO::FETCH_ASSOC);
        if ($result) {
            if ($result['user'] !== $this->getUserId()) {
                throw new \Exception('Users are not allowed to edit foreign Posts', 403);
            }
        } else if ($id) {
            throw new \Exception('This Post does not exist', 404);
        }
        $this->display('post_update.html', ['result' => $result]);        
    }
    public function postSave($data) {
        # TODO: perform mysql query sanitation
        $user = $this->getUser();
        if (!$user) {
            throw new \Exception('Only authenticated Users may create Posts', 401);
        }
        if ($data['id']) {
            if (!is_numeric($data['id'])) {
                return;
            }
            return $this->getDbConnection()->exec("
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
            return $this->getDbConnection()->exec($sql);
        }
    }
    public function postKill($id) {
        if (!$id || !is_numeric($id)) {
            return;
        }
        $uid = $this->getUserId();
        if (!$uid) {
            throw new \Exception("This resource is only available for authenticated users", 401);
        }
        $result = $this->getDbConnection()->exec("DELETE FROM `posts` WHERE `id`='$id' AND `user`='$uid' LIMIT 1;");
        return $this->index();
    }


}
