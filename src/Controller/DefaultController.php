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

        $this->display('post_edit.html', ['result' => $result]);        

    }
    public function postSave($data) {
        # TODO: perform mysql query sanitation
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
                `id`='$data[id]';
                ");     
        } else {
            $sql = "INSERT INTO `posts` (
                `title`, 
                `author`,
                `content`,
                `created_at`,
                `updated_at`
                ) VALUES (
                    '$data[title]',
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
        $result = $this->getDbConnection()->exec("DELETE FROM `posts` WHERE `id`=$id;");
        return $this->index();
    }


}
