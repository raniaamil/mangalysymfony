<?php

namespace App\Controller;

use App\Entity\Manga; // On importe la classe Manga pour manipuler l'entité
use App\Repository\MangaRepository; // On importe le repository pour interagir avec la base de données
use App\Repository\GenreRepository; // On importe le repository pour interagir avec la base de données
use Doctrine\ORM\EntityManagerInterface; // Pour interagir avec la base de données
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // Base des contrôleurs Symfony
use Symfony\Component\HttpFoundation\Request; // Pour gérer les requêtes HTTP
use Symfony\Component\HttpFoundation\Response; // Pour renvoyer des réponses HTTP
use Symfony\Component\Routing\Annotation\Route; // Pour définir les routes

#[Route('/admin/manga')] // Route de base pour le contrôleur Manga
class AdminMangaController extends AbstractController
{
    #[Route('/', name: 'manga_index', methods: ['GET'])] // Route pour afficher la liste des mangas
    public function index(MangaRepository $mangaRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $mangas = $mangaRepository->findAll(); // Récupère tous les mangas de la base de données

        return $this->render('admin/manga/index.html.twig', ['mangas' => $mangas]); // Affiche la vue avec la liste des mangas
    }

    #[Route('/new', name: 'manga_new', methods: ['GET', 'POST'])] // Route pour créer un nouveau manga
    public function new(Request $request, EntityManagerInterface $em, GenreRepository $genreRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $genres = $genreRepository->findAll(); // Retourne tous les genres depuis la base de données

        if ($request->isMethod('POST')) { // Vérifie si la requête est POST
            $manga = new Manga(); // Crée une nouvelle instance de l'entité Manga

            // Récupère les données du formulaire et les attribue au manga
            $manga->setTitre($request->request->get('titre'));
            $manga->setAuteur($request->request->get('auteur'));
            $manga->setDateSortie(new \DateTime($request->request->get('date_sortie')));
            $manga->setDescription($request->request->get('description'));
            $manga->setImage($request->request->get('image'));
            $genreId = $request->request->get('genre_id');
            $genre = $genreRepository->find($genreId);
            
            if (!$genre) {
                throw $this->createNotFoundException('Le genre sélectionné est introuvable.');
            }
            $manga->setGenre($genre);

            $em->persist($manga); // Prépare le manga à être sauvegardé
            $em->flush(); // Sauvegarde dans la base de données

            return $this->redirectToRoute('manga_index'); // Redirige vers la liste des mangas après création
        }

        return $this->render('admin/manga/new.html.twig', [
            'genres' => $genres, // Passage des genres au template
        ]); 
    }

    #[Route('/{id}/edit', name: 'manga_edit', methods: ['GET', 'POST'])] // Route pour modifier un manga existant
    public function edit(Manga $manga, Request $request, EntityManagerInterface $em, GenreRepository $genreRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $genres = $genreRepository->findAll();

        if ($request->isMethod('POST')) { // Vérifie si la requête est POST
            // Met à jour les informations du manga
            $manga->setTitre($request->request->get('titre'));
            $manga->setAuteur($request->request->get('auteur'));
            $manga->setDateSortie(new \DateTime($request->request->get('date_sortie')));
            $manga->setDescription($request->request->get('description'));
            $manga->setImage($request->request->get('image'));
            $genreId = $request->request->get('genre_id');
            $genre = $genreRepository->find($genreId);
            
            if (!$genre) {
                throw $this->createNotFoundException('Le genre sélectionné est introuvable.');
            }
            $manga->setGenre($genre);

            $em->flush(); // Sauvegarde les modifications

            return $this->redirectToRoute('manga_index'); // Redirige vers la liste des mangas
        }

        return $this->render('admin/manga/edit.html.twig', [
            'manga' => $manga, // Affiche le formulaire de modification
            'genres' => $genres, // Passage des genres au template
        ]); 
    }

    #[Route('/{id}/delete', name: 'manga_delete', methods: ['POST'])] // Route pour supprimer un manga
    public function delete(Manga $manga, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $em->remove($manga); // Supprime le manga
        $em->flush(); // Enregistre la suppression dans la base de données

        return $this->redirectToRoute('manga_index'); // Redirige vers la liste des mangas après suppression
    }
}
