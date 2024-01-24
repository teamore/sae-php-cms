<?php

use App\Controller\DefaultController;

require '../src/autoload.php';

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

