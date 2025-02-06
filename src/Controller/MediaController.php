<?php

namespace App\Controller;
use App\Model\Post;
use App\Model\User;
use App\Uploader;


class MediaController extends AbstractController
{
    public function show() {
        $model = $this->query['model'] ?? 'posts';
        $id = $this->query['id'];
        $mediaId = $this->query['media_id'];
        Uploader::show($id, $model, $mediaId);
    }
    public function upload() {
        $model = $this->query['model'] ?? 'posts';
        $id = $this->query['id'];
        $mediaId = $this->query['media_id'];
        $payload = file_get_contents('php://input');
        $media = Uploader::store($payload, $id, $model, $mediaId);    
        if (count($media) > 0) {
            if ($model === 'posts') {
                Post::attachMedia($media, $id);
            } else if ($model === 'users') {
                User::attachMedia($media, $id);
            } 
        }
    }
}
