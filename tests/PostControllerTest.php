<?
use \Test\AbstractTestCase;
use \App\Controller\PostController;

require __DIR__."/../src/autoload.php";
final class PostControllerTest extends AbstractTestCase {
    public function testRouting(): void {
        $controller = new PostController();
        $testId = 1;
        $controller->one($testId);
        $payload = $controller->getPayload();
        $this->assertArrayHasKey('id', $payload['result']);
    }
}