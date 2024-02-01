<?php
namespace App;
use App\Controller\DefaultController;
use App\Controller\UserController;
class Router {
    public function __construct() { 
        $this->start();
    }
    public function readConfig() {
        $config = yaml_parse_file("../routes.yaml");
        var_dump($config);
    }

    public function start() {
        $requestBody = json_decode(file_get_contents('php://input'), true);

        $controller = new DefaultController();

        if (isset($_GET['post_unlike'])) {
            try {
                $controller->postLikeDelete($_GET['post_unlike']);
            } catch (\Exception $e) {
                $controller->addMessage($e->getMessage());
            }
        }

        # ROUTING

        # LOGIN / LOGOUT
        if (isset($_GET['action']) && $_GET['action'] === 'login_show') {
            $userController = new UserController();
            $userController->showLogin();
            die();
        }

        if (isset($_POST['action']) && ($_POST['action'] === 'login')) {
            $userController = new UserController();
            if ($userController->doLogin()) {
                $controller->addMessage('Login successful.');
            }
        }

        if (isset($_GET['action']) && ($_GET['action'] === 'logout')) {
            $userController = new UserController();
            if ($userController->doLogout()) {
                $controller->addMessage('Logout successful.');
            }
        }

        # REGISTRATION

        if (isset($_GET['action']) && ($_GET['action'] === 'user_create')) {
            $userController = new UserController();
            $userController->showSignup();
            die();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (($_POST['model'] ?? false) === 'User') {
                $userController = new UserController();
                $userController->doSignup($_POST);
            }
        }
        if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            if (in_array($requestBody['action'], ['like', 'unlike'])) {
                try {
                    if ($requestBody['action'] === 'like') {
                        $data['result'] = $controller->postLikeSave($_GET['post_id']);
                    } else {
                        $data['result'] = $controller->postLikeDelete($_GET['post_id']);
                    }
                } catch (\PDOException $e) {
                    $data['error'] = $e->getMessage();
                    http_response_code(304);
                }
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode($data);
                die();
            }

        }


        # POST CRUD

        if (isset($_POST['action']) && ($_POST['action'] === 'post_save')) {
            try {
                $controller->postSave($_POST);
            } catch (\Exception $e) {
                $controller->addMessage($e->getMessage());
            }
        }
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            $controller->postKill($requestBody['post_id']);
            die();
        }
    
        if (isset($_GET['action']) && $_GET['action'] === 'post_update') {
            try {
                $controller->postEdit($_GET['post_id'] ?? 0);
            } catch (\Exception $e) {
                $controller->addMessage($e->getMessage());
                $controller->display('error.html');
            }
            die();
        }
        if (isset($_GET['post_id'])) {
            $controller->postShow($_GET['post_id']);
            die();
        }
        $controller->index();

    }
}