<?
namespace App\Controller;
class AbstractController {
    protected $loader;
    protected $twig;
    protected $messages = [];
    public function __construct() {
        $this->loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/../../templates');
        $this->twig = new \Twig\Environment($this->loader);    
    }
    public function addMessage($message) {
        $this->messages[] = $message;
    }
    public function display($template, $payload = []) {
        $payload['user'] = $_SESSION['user'] ?? null;
        $payload['messages'] = $this->messages;
        $this->twig->display($template, $payload);
    }
    public function getDbConnection() {
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
        return $pdo;
    }    
}