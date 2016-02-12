<?php
namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ApplicationAvailabilityTest extends WebTestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    private $clientAnon;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    private $clientAuth;

    public function setUp()
    {
        $this->clientAnon = static::createClient();
        $this->clientAuth = static::createClient(
            array(),
            array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW' => 'admin')
        );
    }

    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful($url)
    {
        $this->clientAuth->request('GET', $url);
        $this->assertEquals(Response::HTTP_OK,$this->clientAuth->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider urlProvider
     */
    public function testPageIsRedirectToLogin($url)
    {
        $this->clientAnon->request('GET', $url);
        $this->assertTrue($this->clientAnon->getResponse()->isRedirect('http://localhost/login'));
    }

    /**
     * @return array
     */
    public function urlProvider()
    {
        return array(
            array('/'),
            array('/accounting'),
            array('/accounting/add'),
            array('/accounting/category'),
            array('/member'),
            array('/member/add'),
            array('/param/password'),
            array('/param/rank'),
            array('/param/season'),
        );
    }
}