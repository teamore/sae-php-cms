<?php

use App\Controller\DefaultController;

require '../vendor/autoload.php';

spl_autoload_register(function ($class) {
    $class = str_replace("\\",DIRECTORY_SEPARATOR,$class);
    $search = [str_replace("App","/../src",$class), str_replace("Twig","/../vendor/twig",$class)];
    foreach($search as $qcn) {
        if (file_exists(__DIR__ . $qcn . '.php')) {
            require_once(__DIR__ . $qcn . '.php');
            return;
        }
    }
});

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

