<?php

namespace App\Tests;

use App\Entity\User;
use App\Entity\Post;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserCreation()
    {
        // 1. Créer un utilisateur
        $user = new User();
        
        // 2. Définir des données de test
        $username = "testuser";
        $email = "test@example.com";
        
        // 3. Appliquer les données au modèle
        $user->setUsername($username);
        $user->setEmail($email);
        
        // 4. Vérifier que les getters retournent les bonnes valeurs
        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($email, $user->getEmail());
        $this->assertContains('ROLE_USER', $user->getRoles());
    }
}



