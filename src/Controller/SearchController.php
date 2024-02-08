<?php
namespace App\Controller;

class SearchController extends AbstractController {
    public function index() {
        $searchterm = $this->query['searchterm'] ?? null;
        if (isset($searchterm)) {
            $sql = "SELECT p.*, u.username, 'posts' as `type` FROM `posts` p 
                LEFT JOIN `users` u ON u.`id`=p.`user` 
                WHERE p.`content` LIKE '%$searchterm%' 
                OR p.`title` LIKE '%$searchterm%'
                OR u.`username` LIKE '%$searchterm%'
                ;";
            $postResults = $this->db()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            $sql = "SELECT *, 'users' as `type` FROM `users` WHERE `username` LIKE '%$searchterm%';";
            $userResults = $this->db()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            $searchResults = array_merge($postResults, $userResults);
        }
        $this->setView("index.html", ["searchterm"=> $searchterm,"results" => $searchResults ?? []]);
    }
}