<?php

namespace App\Tests;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testSetandGetUsername(): void
    {
        $user = new User();

        $user->setUsername('JeandelaRue92');
        $response = $user->getUsername();

        $this->assertIsString($response, 'Le username doit être une chaîne de caractères.');
        $this->assertSame('JeandelaRue92', $response, 'Le username renvoyé doit correspondre à celui défini.');
    }
}







