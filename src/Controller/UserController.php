<?php
namespace App\Controller;
use App\Controller\AbstractController;
class UserController extends AbstractController {
    public function showLogin() {
        $this->twig->display("login.html");
    }
    public function doLogin() {
        $user = $this->getDbConnection()->query("SELECT * FROM `users` WHERE `username`='$_POST[username]' AND `password`='$_POST[password]';")->fetchObject();
        if ($user === false) {
            return;
            # Authentication Error
        } else {
            $_SESSION['user'] = $user;
            echo "login successful.";
        }
    }
    public function doLogout() {
        if (isset($_SESSION['user'])) {
            unset($_SESSION["user"]);
            echo "logout successful.";    
        }
    }
}