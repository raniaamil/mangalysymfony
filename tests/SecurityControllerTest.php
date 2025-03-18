<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginPageDisplaysForm()
    {
        $client = static::createClient();
        
        $client->request('GET', '/login');
        
        $this->assertResponseIsSuccessful();
        
        $this->assertSelectorExists('form[action="/login"]');
        
        $this->assertSelectorExists('input[name="_username"]');
        $this->assertSelectorExists('input[name="_password"]');
        $this->assertSelectorExists('button[type="submit"]');
    }
}


