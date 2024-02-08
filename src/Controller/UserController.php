<?php
namespace App\Controller;
use App\Controller\AbstractController;
class UserController extends AbstractController {
    public function showLogin() {
        $this->setView("login.html");
    }
    public function showSignup() {
        $this->setView("signup.html");
    }
    public function userShow() {
        $uid = $this->query['user_id'];
        $result = $this->db()->query("SELECT * FROM `users` WHERE `id`='$uid';")->fetch(\PDO::FETCH_ASSOC);
        $this->setView("user.html", ["result" => $result]);
    }
    public function edit() {
        $this->setView("user_edit.html");
    }
    public function doLogin(): object|bool {
        $user = $this->db()->query("
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
                'username' => ['min' =>3, 'max' => 80]
            ];
        
        $uid = $this->getUserId();
        if (!$uid) {
            $requirements['password'] = ['min' =>5, 'max' => 80];
        }
        
        $errors = $this->validateDataset($data, $requirements);
        if (sizeof($errors) === 0)
            {
                if ($uid) {
                    $sql = "UPDATE `users` SET 
                        `username`='$data[username]', 
                        `email`='$data[email]',
                        `updated_at`='".date('Y-m-d H:i:s')."'
                        WHERE `id`='$uid';";
                    $this->db()->exec($sql);
                } else {
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
                    $this->db()->exec($sql);    
                }
                return true;
        } else {
            $this->addMessage("Please correct the given user information.");
            foreach ($errors as $key => $error) {
                $this->addMessage($key." is not correct.");
            }
            $this->setView('signup.html',['signup' => $data]);
        }
    }
}