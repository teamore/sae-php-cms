<?php
namespace App\Traits;
trait Softdeletable {
    private $deletedAt;

    public function setDeletedAt($deletedAt) {
        $this->deletedAt = $deletedAt;
    }
    public function getDeletedAt() {
        return $this->deletedAt;
    }

}