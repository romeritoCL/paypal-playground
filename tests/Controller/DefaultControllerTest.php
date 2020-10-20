<?php

namespace App\Tests\Controller;

use App\Controller\DefaultController;
use App\Security\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class PostControllerTest
 * @package App\Test
 */
class DefaultControllerTest extends WebTestCase
{
    /**
     * testIndex
     */
    public function testIndex()
    {
        $client = static::createClient();
        $client->loginUser(new User('john@paypal.com'));
        $container = self::$container;
        $indexRoute = $container->get('router')->generate('index');
        $client->request('GET', $indexRoute);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString(
            "PayPal Products",
            $client->getResponse()->getContent()
        );
    }
}
