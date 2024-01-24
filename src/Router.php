<?php
namespace App;
use App\Controller\DefaultController;
class Router {
    public function __construct() { 
        $this->start();
    }
    public function readConfig() {
        $config = yaml_parse_file("../routes.yaml");
        var_dump($config);
    }
    public function start() {
        echo $_SERVER['REQUEST_METHOD'];
        $controller = new DefaultController();

        # ROUTING
        if (isset($_GET['action']) && $_GET['action'] === 'post_save') {
            $controller->postSave($_GET);
        }
        if (isset($_GET['action']) && $_GET['action'] === 'kill') {
            $controller->postKill($_GET['post_id']);
            die();
        }
    
        if (isset($_GET['action']) && $_GET['action'] === 'edit') {
            $controller->postEdit($_GET['post_id'] ?? 0);
            die();
        }
        if (isset($_GET['post_id'])) {
            $controller->postShow($_GET['post_id']);
            die();
        }
        $controller->index();

    }
}