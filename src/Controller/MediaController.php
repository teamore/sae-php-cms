<?php

namespace App\Controller;
use App\Uploader;


class MediaController extends AbstractController
{
    public function show() {
        $model = $this->query['model'] ?? 'posts';
        $id = $this->query['id'];
        $mediaId = $this->query['media_id'];
        Uploader::show($id, $model, $mediaId);
    }
}
