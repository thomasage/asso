<?php
namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}