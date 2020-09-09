<?php

namespace App\Tests\Controller\Paypal;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class AnonymousControllerTest
 * @package App\Tests\Controller\Braintree
 */
class AnonymousControllerTest extends WebTestCase
{
    /**
     * testIndex
     */
    public function testIndex()
    {
        $client = static::createClient();
        $container = self::$container;
        $indexRoute = $container->get('router')->generate('paypal-index');
        $anonymousHomeRoute = $container->get('router')->generate('paypal-anonymous-home');
        $client->request('GET', $indexRoute);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertResponseRedirects($anonymousHomeRoute);
    }
}
