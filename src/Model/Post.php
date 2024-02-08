<?php
namespace App\Model;
use App\Traits\Softdeletable;
use App\Traits\Timestampable;
class Post extends AbstractModel {
    use Timestampable, Softdeletable;
    protected $id;
    protected $title;
    protected $content;
    protected $author;
}