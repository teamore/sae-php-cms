<?
use \Test\AbstractTestCase;
use \App\Controller\DefaultController;

require __DIR__."/../src/autoload.php";
final class DefaultControllerTest extends AbstractTestCase {
    public function testRouting(): void {
        $controller = new DefaultController();
        $result = $controller->postShow(1);
        echo $result;
        $this->assertSame("","");
    }
}