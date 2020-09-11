<?php

namespace App\Tests\Controller\Paypal;

use App\Controller\Paypal\AbstractController;
use App\Controller\Paypal\DefaultController;
use App\Service\PaypalService;
use App\Service\SessionService;
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
        $container = self::$container;
        $indexRoute = $container->get('router')->generate('paypal-index');
        $anonymousIndexRoute = $container->get('router')->generate('paypal-anonymous-index');
        $client->request('GET', $indexRoute);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertResponseRedirects($anonymousIndexRoute);
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

        $privateProperty = $this->getInvisibleProperty('sessionService', $defaultController);
        $this->assertInstanceOf(SessionService::class, $privateProperty);
        $privateProperty = $this->getInvisibleProperty('paypalService', $defaultController);
        $this->assertInstanceOf(PaypalService::class, $privateProperty);
    }
}
