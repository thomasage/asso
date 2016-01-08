<?php
namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends WebTestCase
{
    public function testAnonymousUsersCannotAccessToTheDashboard()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertEquals(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
    }

    public function testAuthenticatedUsersCanAccessToTheDashboard()
    {
        $client = static::createClient(
            array(),
            array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW' => 'admin')
        );

        $crawler = $client->request('GET', '/');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $this->assertEquals(1, $crawler->filter('div#menu-primary')->count());
    }
}