<?php
namespace App\Controller;
use App\Controller\AbstractController;
class UserController extends AbstractController {
    public function showLogin() {
        $this->display("login.html");
    }
    public function showSignup() {
        $this->display("signup.html");
    }
    public function doLogin(): object|bool {
        $user = $this->getDbConnection()->query("
            SELECT * FROM `users` WHERE 
            `username`='$_POST[username]' AND 
            `password`='".hash('sha256', $_POST['password'])."';
        ")->fetchObject();
        if ($user === false) {
            throw new \Exception("Authentication Failed.", 401);
            # Authentication Error
        } else {
            $_SESSION['user'] = $user;
            $this->addMessage('Login successful.');
            return $user;
        }
    }
    public function doLogout(): bool {
        if ($this->getUser()) {
            unset($_SESSION["user"]);
            $this->addMessage("Logout successul.");
            return true;
        }
        return false;
    }
    public function validate(string $data, array $requirements):bool|array {
        $errors = [];
        if (isset($requirements['min']) && (strlen($data) < $requirements['min'])) {
            $errors[] = 'min';
        }
        if (isset($requirements['max']) && (strlen($data) > $requirements['max'])) {
            $errors[] = 'max';
        }
        if (isset($requirements['regex']) && !preg_match($requirements['regex'], $data)) {
            $errors[] = 'regex';
        }
        if (sizeof($errors) > 0) {
            return $errors;
        } else {
            return true;
        }
    }
    public function validateDataset(array $data, array $requirements) {
        $errors = [];
        foreach ($requirements as $key=>$requirement) {
            $result = $this->validate($data[$key], $requirement);
            if ($result !== true) {
                $errors[$key] = $result;
            }
        }
        return $errors;
    }

    public function doSignup($data = null) {
        $data = $data ?? $_REQUEST;
        $requirements = 
            [
                'email' => ['regex' => '/^\S+@\S+\.\S+$/'],
                'username' => ['min' =>3, 'max' => 80],
                'password' => ['min' =>5, 'max' => 80]
            ];
        
        $errors = $this->validateDataset($data, $requirements);
        if (sizeof($errors) === 0)
            {
                $data['password'] = hash('sha256', $data['password']);
                $sql = "INSERT INTO `users` (
                    `username`, 
                    `email`,
                    `password`,
                    `created_at`,
                    `updated_at`
                    ) VALUES (
                        '$data[username]',
                        '$data[email]',
                        '$data[password]',
                        '".date('Y-m-d H:i:s')."',
                        '".date('Y-m-d H:i:s')."'
        
                    );";
                return $this->getDbConnection()->exec($sql);
        } else {
            $this->addMessage("Please correct the given user information.");
            foreach ($errors as $key => $error) {
                $this->addMessage($key." is not correct.");
            }
            $this->display('signup.html',['signup' => $data]);
            die();
        }
    }
}