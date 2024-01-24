<?
spl_autoload_register(function ($class) {
    $class = str_replace("\\",DIRECTORY_SEPARATOR,$class);
    $search = [
        preg_replace("%^App%","/../src",$class), 
        preg_replace("%^Test%","/../tests",$class), 
        preg_replace("%^Twig%","/../vendor/twig",$class)];
    foreach($search as $qcn) {
        if (file_exists(__DIR__ . $qcn . '.php')) {
            require_once(__DIR__ . $qcn . '.php');
            return;
        }
    }
});

require __DIR__.'/../vendor/autoload.php';