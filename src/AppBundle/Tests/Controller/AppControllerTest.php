<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AppControllerTest extends WebTestCase
{
    public function testApp()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/app');
    }

}
