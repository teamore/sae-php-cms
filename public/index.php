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

$host = getenv('MYSQL_HOST') ?: 'sae-php-cms-mysql';
$database = getenv('MYSQL_DATABASE');
$username = getenv('MYSQL_USER');
$password = getenv('MYSQL_PASSWORD');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
$controller = new DefaultController();
$controller->index();

?>
