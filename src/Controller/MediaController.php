<?php

namespace App\Controller;
use App\Uploader;


class MediaController extends AbstractController
{
    public function show() {
        $postId = $this->query['post_id'];
        $mediaId = $this->query['media_id'];
        Uploader::show($postId, $mediaId);
    }
}
