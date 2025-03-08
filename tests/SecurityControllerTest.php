<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginPageDisplaysForm()
    {
        // 1. Créer un client HTTP
        $client = static::createClient();
        
        // 2. Faire une requête à la page de connexion
        $client->request('GET', '/login');
        
        // 3. Vérifier que la réponse est un succès (code HTTP 200)
        $this->assertResponseIsSuccessful();
        
        // 4. Vérifier que le formulaire de connexion est présent
        $this->assertSelectorExists('form[action="/login"]');
        
        // 5. Vérifier la présence des champs du formulaire
        $this->assertSelectorExists('input[name="_username"]');
        $this->assertSelectorExists('input[name="_password"]');
        $this->assertSelectorExists('button[type="submit"]');
    }
}


