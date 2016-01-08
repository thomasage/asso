<?php
namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class MemberControllerTest extends WebTestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    private $client;

    public function setUp()
    {
        $this->client = static::createClient(
            array(),
            array('PHP_AUTH_USER' => 'admin', 'PHP_AUTH_PW' => 'admin')
        );
    }

    public function testIndex()
    {
        $crawler = $this->client->request('GET', '/member');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.app_member_index')->count());
        $this->assertEquals(1, $crawler->selectButton('search')->count());

        $button = $crawler->selectButton('search')->first();
        $form = $button->form(
            array(
                'member_search[firstname]' => 'John',
            )
        );
        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());

        $crawler = $this->client->followRedirect();
        $this->assertEquals(1, $crawler->filter('div.app_member_index')->count());
    }

    public function testPhoto()
    {
        $crawler = $this->client->request('GET', '/member');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.app_member_index')->count());
        $this->assertGreaterThanOrEqual(1, $crawler->filter('td.photo > img')->count());

        $this->client->request('GET', $crawler->filter('td.photo > img')->attr('src'));
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testAdd()
    {
        $crawler = $this->client->request('GET', '/member/add');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.app_member_add')->count());
        $this->assertEquals(1, $crawler->selectButton('add')->count());

        $button = $crawler->selectButton('add')->first();
        $form = $button->form(
            array(
                'member[firstname]' => 'John',
                'member[lastname]' => 'Doe',
                'member[gender]' => 'm',
                'member[birthday]' => '1980-01-01',
                'member[birthplace]' => 'New-York City',
                'member[address]' => 'Main street',
                'member[zip]' => '01234',
                'member[city]' => 'Washington D.C.',
            )
        );
        $form['member[photo]']->upload(__DIR__.'/../../photo.jpg');
        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());

        $crawler = $this->client->followRedirect();
        $this->assertEquals(1, $crawler->filter('div.flash.success')->count());
    }

    public function testShow()
    {
        $crawler = $this->client->request('GET', '/member');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.app_member_index')->count());
        $this->assertGreaterThanOrEqual(1, $crawler->selectLink('Show')->count());

        $crawler = $this->client->click($crawler->selectLink('Show')->first()->link());
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.app_member_show')->count());
    }

    public function testEdit()
    {
        $crawler = $this->client->request('GET', '/member');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.app_member_index')->count());
        $this->assertGreaterThanOrEqual(1, $crawler->selectLink('Show')->count());

        $crawler = $this->client->click($crawler->selectLink('Show')->first()->link());
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.app_member_show')->count());
        $this->assertEquals(1, $crawler->selectLink('Edit')->count());

        $crawler = $this->client->click($crawler->selectLink('Edit')->first()->link());
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('div.app_member_edit')->count());
    }
}