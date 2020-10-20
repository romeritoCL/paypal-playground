<?php

namespace App\Tests\Controller\Paypal;

use App\Controller\Paypal\AbstractController;
use App\Controller\Paypal\DefaultController;
use App\Security\User;
use App\Service\PaypalService;
use App\Service\AbstractSessionService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Tests\Helper\InvisiblePropertiesTrait;
use ReflectionException;

/**
 * Class DefaultControllerTest
 * @package App\Tests\Controller\Paypal
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
        $client->loginUser(new User('john@paypal.com'));
        $container = self::$container;
        $indexRoute = $container->get('router')->generate('paypal-index');
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
        $privateProperty = $this->getInvisibleProperty('paypalService', $defaultController);
        $this->assertInstanceOf(PaypalService::class, $privateProperty);
    }
}
