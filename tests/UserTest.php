<?php

namespace App\Tests;

use App\Entity\User;
use App\Entity\Post;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserCreation()
    {
        $user = new User();
        
        $username = "testuser";
        $email = "test@example.com";
        
        $user->setUsername($username);
        $user->setEmail($email);
        
        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($email, $user->getEmail());
        $this->assertContains('ROLE_USER', $user->getRoles());
    }
}





