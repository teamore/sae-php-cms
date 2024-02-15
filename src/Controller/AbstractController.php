<?
namespace App\Controller;
use App\Database;
class AbstractController {
    protected \Twig\Loader\FilesystemLoader $loader;
    protected \Twig\Environment $twig;
    protected Array $messages = [];
    protected ?Array $query = null;
    protected ?String $view = null;
    protected ?Array $requestBody = null;
    protected Object|Array|null $payload = null;
    public function __construct($query = null) {
        $this->loader = new \Twig\Loader\FilesystemLoader(__DIR__.'/../../templates');
        $this->twig = new \Twig\Environment($this->loader, ['debug' => true]);    
        $this->twig->addExtension(new \Twig\Extension\DebugExtension());
        if (isset($query)) {
            $this->setQuery($query);
        }
        $this->requestBody = json_decode(file_get_contents('php://input'), true);
    }
    public function setView(?String $view, Object|Array|null $payload = null) {
        if (isset($view)) {
            $this->view = $view;
        }
        if (isset($payload)) {
            $this->payload = array_merge($this->payload ?? [], $payload);
        }
        if (in_array("App\\Traits\\Paginatable", class_uses($this) ?? [])) {
            $this->addPaginatorToPayload($this->payload);        
        }
    }
    public function getView(): ?String {
        return $this->view;
    }
    public function setPayload(Object|Array|null $payload) {
        $this->payload = $payload;
    }
    public function getPayload(): Object|Array|null {
        return $this->payload;
    }
    public function setQuery($query) {
        $this->query = $query;
    }
    public function addMessage(String|Array $message) {
        $this->messages[] = $message;
    }
    public function getMessages(): ?Array {
        return $this->messages;
    }
    public function setMessages(?Array $messages) {
        $this->messages = $messages;
    }
    public function getUser():Object|null {
        return $_SESSION['user'] ?? null;
    }
    public function getUserId($authenticationRequired = false):int|null {
        $uid = ($this->getUser() ? $this->getUser()->id : null);
        if (!$uid && $authenticationRequired) {
            throw new \Exception("This resource is only available for authenticated users", 401);
        }
        return $uid;
    }
    public function display($viewTemplate = null, $payload = null) {
        if ($viewTemplate) {
            $this->setView($viewTemplate);
        }
        $payload = $payload ?? $this->getPayload();
        $payload['user'] = $payload['user'] ?? $this->getUser() ?? null;
        $payload['messages'] = $payload['messages'] ?? $this->messages;
        if ($payload) {
            $this->setPayload($payload);
        }
        $view = $this->getView();
        if (!isset($view)) {
            throw new \Exception("No View has been defined for this Action.", 500);
        }
        $this->twig->display($view, $this->getPayload());
    }
    public function db() {
        try {
            $pdo = Database::connect();
        } catch (\PDOException $e) {
            $this->addMessage("Connection failed: " . $e->getMessage());
        }
        return $pdo;
    }    
}