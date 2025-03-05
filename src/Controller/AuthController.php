<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(UserInterface $user): JsonResponse
    {
        // Cette méthode ne fait rien - l'authentification JWT se charge de tout
        // Le token est généré automatiquement par le bundle JWT

        return new JsonResponse(['message' => 'Authentification réussie']);
    }
}