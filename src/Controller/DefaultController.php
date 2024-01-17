<?php

namespace App\Controller;


class DefaultController
{
    private $loader;
    private $twig;
    public function __construct() {
        $this->loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/../../templates');
        $this->twig = new \Twig\Environment($this->loader);    
    }
    
    public function index()
    {
        # database connection parameters
        $host = getenv('MYSQL_HOST') ?: 'sae-php-cms-mysql';
        $database = getenv('MYSQL_DATABASE');
        $username = getenv('MYSQL_USER');
        $password = getenv('MYSQL_PASSWORD');

        # establish database connection
        try {
            $pdo = new \PDO("mysql:host=$host;dbname=$database", $username, $password);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }

        # retrieve results
        $results = $pdo->query("SELECT * FROM `posts`;")->fetchAll(\PDO::FETCH_ASSOC);

        # call view
        $this->twig->display('index.html', ['title' => 'CMS Application', 'body' => 'index controller!', 'results' => $results]);
    }
}
