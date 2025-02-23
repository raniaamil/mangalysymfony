<?php

namespace App\Controller;

use App\Entity\Genre; // On importe la classe Genre pour manipuler l'entité
use App\Repository\GenreRepository; // On importe le repository pour interagir avec la base de données
use Doctrine\ORM\EntityManagerInterface; // Pour interagir avec la base de données
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // Base des contrôleurs Symfony
use Symfony\Component\HttpFoundation\Request; // Pour gérer les requêtes HTTP
use Symfony\Component\HttpFoundation\Response; // Pour renvoyer des réponses HTTP
use Symfony\Component\Routing\Annotation\Route; // Pour définir les routes

#[Route('/admin/genres')]
class AdminGenreController extends AbstractController
{
    #[Route('/', name: 'genre_index', methods: ['GET'])] // Route pour afficher la liste des genre
    public function index(GenreRepository $genreRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN') ;
        $genre = $genreRepository->findAll(); // Récupère tous les genre de la base de données

        return $this->render('admin/genres/index.html.twig', ['genre' => $genre]); // Affiche la vue avec la liste des genres
    }

    #[Route('/new', name: 'genre_new', methods: ['GET', 'POST'])] 
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN') ;
        if ($request->isMethod('POST')) { 
            
            $genre = new Genre(); 

            $genre->setNom($request->request->get('nom'));

            $em->persist($genre); 
            $em->flush(); 

            return $this->redirectToRoute('genre_index'); 
        }

        return $this->render('admin/genres/new.html.twig'); 
    }

    #[Route('/{id}/edit', name: 'genre_edit', methods: ['GET', 'POST'])] // Route pour modifier un genre existant
    public function edit(Genre $genre, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN') ;
        if ($request->isMethod('POST')) { // Vérifie si la requête est POST
            // Met à jour les informations du genre
            $genre->setNom($request->request->get('nom')); // Modifie le nom

            $em->flush(); // Sauvegarde les modifications

            return $this->redirectToRoute('genre_index'); // Redirige vers la liste des genres
        }

        return $this->render('admin/genres/edit.html.twig', ['genre' => $genre]); // Affiche le formulaire de modification
    }

    #[Route('/{id}/delete', name: 'genre_delete', methods: ['POST'])] // Route pour supprimer un genre
    public function delete(Genre $genre, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN') ;
        $em->remove($genre); // Supprime le genre
        $em->flush(); // Enregistre la suppression dans la base de données

        return $this->redirectToRoute('genre_index'); // Redirige vers la liste des genres après suppression
    }
}
