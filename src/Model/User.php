<?php
namespace App\Model;
use App\Traits\Softdeletable;
use App\Traits\Timestampable;
class User extends AbstractModel {
    use Timestampable, Softdeletable;
    protected static $table = "users";
}