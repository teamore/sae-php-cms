<?php
namespace App\Model;
use App\Traits\Softdeletable;
use App\Traits\Timestampable;
class User {
    use Timestampable, Softdeletable;
}