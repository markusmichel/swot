<?php

namespace Swot\NetworkBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ThingControllerTest extends WebTestCase
{
    public function testDeletething()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/{id}/delete');
    }

}
