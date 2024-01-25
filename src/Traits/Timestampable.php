<?php
namespace App\Traits;
trait Timestampable {
    private $createdAt;
    private $updatedAt;

    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }
    public function getCreatedAt() {    
        return $this->createdAt;
    }
    public function setUpdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;  
    }
    public function getUpdatedAt() {   
        return $this->updatedAt;
    }
}