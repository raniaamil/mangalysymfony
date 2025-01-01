<?php

namespace App\Controller;

use App\Repository\MangaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PublicMangaController extends AbstractController
{
    #[Route('/manga/suggestions', name: 'manga_suggestions', methods: ['GET'])]
    public function suggestions(Request $request, MangaRepository $repository): JsonResponse
    {
        // Récupère la requête utilisateur
        $query = $request->query->get('q', '');

        // Vérifie que la requête contient au moins deux caractères
        if (strlen($query) < 2) {
            return $this->json([], 400); // Renvoie un statut 400 si la requête est trop courte
        }

        // Recherche des mangas correspondant à la requête
        $mangas = $repository->findByQuery($query);

        // Retourne les résultats au format JSON
        return $this->json(
            $mangas,
            200,
            [],
            ['groups' => 'manga_suggestion'] // Groupe de sérialisation pour limiter les champs renvoyés
        );
    }
}

