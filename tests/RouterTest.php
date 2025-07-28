<?php
use App\Router;
use \Test\AbstractTestCase;

require __DIR__."/../src/autoload.php";
final class RouterTest extends AbstractTestCase {
    public function testRouting(): void {
        // TODO: mount configuration folder instead of copying routes.yaml
        $router = new Router('./config/routes.yaml', false);
        ob_start();
        $testId = 2;
        $router->run('/posts/'.$testId, 'GET');
        $output = ob_get_clean();
        $result = json_decode($output, true)['payload']['result'];
        $this->assertArrayHasKey('id', $result);;
    }
}