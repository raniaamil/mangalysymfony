<?php

namespace App\Controller;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(UserInterface $user, JWTTokenManagerInterface $jwtManager): JsonResponse
    {
        if (!$user) {
            return new JsonResponse(['message' => 'Non authentifié'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Générer le token JWT pour l'utilisateur connecté
        $token = $jwtManager->create($user);

        return new JsonResponse([
            'message' => 'Authentification réussie',
            'token' => $token,
        ]);
    }
}