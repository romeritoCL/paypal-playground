<?php

namespace App\Tests\Controller\Braintree;

use App\Controller\Braintree\AbstractController;
use App\Controller\Braintree\DefaultController;
use App\Service\BraintreeService;
use App\Tests\Helper\InvisiblePropertiesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use ReflectionException;

/**
 * Class DefaultControllerTest
 * @package App\Tests\Controller\Braintree
 */
class DefaultControllerTest extends WebTestCase
{
    use InvisiblePropertiesTrait;

    /**
     * testIndex
     */
    public function testIndex()
    {
        $client = static::createClient();
        $container = self::$container;
        $indexRoute = $container->get('router')->generate('braintree-index');
        $client->request('GET', $indexRoute);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString(
            "Let's Play",
            $client->getResponse()->getContent()
        );
    }

    /**
     * testInheritance
     *
     * @throws ReflectionException
     */
    public function testInheritance()
    {
        static::bootKernel();
        $container = self::$container;
        $defaultController = $container->get(DefaultController::class);
        $this->assertInstanceOf(AbstractController::class, $defaultController);
        $this->assertInstanceOf(DefaultController::class, $defaultController);

        $property = $this->getInvisibleProperty('braintreeService', $defaultController);
        $this->assertInstanceOf(BraintreeService::class, $property);
    }
}
