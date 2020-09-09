<?php

namespace App\Tests\Controller\Paypal;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class DefaultControllerTest
 * @package App\Tests\Controller\Braintree
 */
class DefaultControllerTest extends WebTestCase
{
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
}
