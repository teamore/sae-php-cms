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
$controller->index();

?>
