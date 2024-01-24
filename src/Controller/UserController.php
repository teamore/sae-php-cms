<?php
namespace App\Controller;
use App\Controller\AbstractController;
class UserController extends AbstractController {
    public function showLogin() {
        $this->twig->display("login.html");
    }
    public function doLogin(): object|bool {
        $user = $this->getDbConnection()->query("SELECT * FROM `users` WHERE `username`='$_POST[username]' AND `password`='$_POST[password]';")->fetchObject();
        if ($user === false) {
            return false;
            # Authentication Error
        } else {
            $_SESSION['user'] = $user;
            return $user;
        }
    }
    public function doLogout(): bool {
        if (isset($_SESSION['user'])) {
            unset($_SESSION["user"]);
            return true;
        }
        return false;
    }
}