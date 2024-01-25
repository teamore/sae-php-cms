<?php
namespace App\Model;
use App\Traits\Softdeletable;
use App\Traits\Timestampable;
class Post {
    use Timestampable, Softdeletable;
    protected $id;
    protected $title;
    protected $content;
    protected $author;
}