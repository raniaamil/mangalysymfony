<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TheorieControllerTest extends WebTestCase
{
    public function testShowTheorie(): void
    {
        $client = static::createClient();

        $client->request('GET', '/theories/8');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'One Piece');

    }
}





