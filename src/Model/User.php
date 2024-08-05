<?php
namespace App\Model;
use App\Traits\MediaAttachable;
use App\Traits\Softdeletable;
use App\Traits\Timestampable;
class User extends AbstractModel {
    use Timestampable, Softdeletable, MediaAttachable;
    protected static $table = "users";
}