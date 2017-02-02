<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GoogleOAuthControllerTest extends WebTestCase
{
    public function testGetauthenticationcode()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/oauth/google/auth');
    }

    public function testGetaccesscoderedirect()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/oauth/google/redirect');
    }

}
