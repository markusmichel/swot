<?php

namespace Swot\NetworkBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RentalControllerTest extends WebTestCase
{
    public function testQuit()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/quit');
    }

}
