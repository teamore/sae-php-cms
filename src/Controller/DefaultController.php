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
        $this->twig->display('index.html', ['title' => 'CMS Application', 'body' => 'index controller!']);
    }
}
