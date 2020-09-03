<?php

namespace App\Tests\Controller;

use App\Controller\DefaultController;
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
        $container = self::$container;
        $indexRoute = $container->get('router')->generate('index');
        $anonymousRoute = $container->get('router')->generate('anonymous-home');
        $client->request('GET', $indexRoute);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertResponseRedirects($anonymousRoute);
    }

    /**
     * testFavicon
     */
    public function testFavicon()
    {
        $client = static::createClient();
        $container = self::$container;
        $faviconRoute = $container->get('router')->generate('favicon');
        $client->request('GET', $faviconRoute);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertResponseRedirects(DefaultController::PAYPAL_FAVICON_URL);
    }
}
