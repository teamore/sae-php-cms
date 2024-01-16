<?php
require '../vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/../templates');
$twig = new \Twig\Environment($loader);

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

echo $twig->render('index.html', ['name' => 'My Blog']);
?>
