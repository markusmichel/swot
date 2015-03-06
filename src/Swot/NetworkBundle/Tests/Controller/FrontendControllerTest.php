<?php

namespace Swot\NetworkBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FrontendControllerTest extends WebTestCase
{
    public function testNewsfeed()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');
    }

}
