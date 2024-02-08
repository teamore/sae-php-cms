<?php

namespace App\Controller;


class MediaController extends AbstractController
{
    public function show() {
        $postId = $this->query['post_id'];
        $mediaId = $this->query['media_id'];
        $result = $this->db()->query("SELECT media FROM `posts` WHERE `id`='$postId';")->fetchColumn();
        $media = json_decode($result, true);
        $file = $media[$mediaId ? $mediaId : 0];
        $filename = $this->uploadPath . $file['path'];
        header("Content-Type: $file[type]");
        header("Content-Length: ".filesize($filename));
        echo file_get_contents($filename);
        die();
    }
}
