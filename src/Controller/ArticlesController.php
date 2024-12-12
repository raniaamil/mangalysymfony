<?php

namespace App\Controller;

use App\Entity\Articles; // On importe la classe Articles pour manipuler l'entité
use App\Repository\ArticlesRepository; // On importe le repository pour interagir avec la base de données
use Doctrine\ORM\EntityManagerInterface; // Pour interagir avec la base de données
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // Base des contrôleurs Symfony
use Symfony\Component\HttpFoundation\Request; // Pour gérer les requêtes HTTP
use Symfony\Component\HttpFoundation\Response; // Pour renvoyer des réponses HTTP
use Symfony\Component\Routing\Annotation\Route; // Pour définir les routes

#[Route('/articles')] // Route de base pour le contrôleur Articles
class ArticlesController extends AbstractController
{
    #[Route('/', name: 'articles_index', methods: ['GET'])] // Route pour afficher la liste des articles
    public function index(ArticlesRepository $articlesRepository): Response
    {
        $articles = $articlesRepository->findAll(); // Récupère tous les articles de la base de données

        return $this->render('articles/index.html.twig', ['articles' => $articles]); // Affiche la vue avec la liste des articles
    }

    #[Route('/new', name: 'articles_new', methods: ['GET', 'POST'])] // Route pour créer un nouvel article
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) { // Vérifie si la requête est POST
            $article = new Articles(); // Crée une nouvelle instance de l'entité Articles

            // Récupère les données du formulaire et les attribue à l'article
            $article->setTitre($request->request->get('titre')); // Attribue le titre
            $article->setContenu($request->request->get('contenu')); // Attribue le contenu
            $article->setGenre($request->request->get('genre')); // Attribue la catégorie

            $em->persist($article); // Prépare l'article à être sauvegardé
            $em->flush(); // Sauvegarde dans la base de données

            return $this->redirectToRoute('articles_index'); // Redirige vers la liste des articles après création
        }

        return $this->render('articles/new.html.twig'); // Affiche le formulaire de création
    }

    #[Route('/{id}/edit', name: 'articles_edit', methods: ['GET', 'POST'])] // Route pour modifier un article existant
    public function edit(Articles $article, Request $request, EntityManagerInterface $em): Response
    {
        if ($request->isMethod('POST')) { // Vérifie si la requête est POST
            // Met à jour les informations de l'article
            $article->setTitre($request->request->get('titre')); // Modifie le titre
            $article->setContenu($request->request->get('contenu')); // Modifie le contenu
            $article->setGenre($request->request->get('genre')); // Modifie le genre

            $em->flush(); // Sauvegarde les modifications

            return $this->redirectToRoute('articles_index'); // Redirige vers la liste des articles
        }

        return $this->render('articles/edit.html.twig', ['article' => $article]); // Affiche le formulaire de modification
    }

    #[Route('/{id}/delete', name: 'articles_delete', methods: ['POST'])] // Route pour supprimer un article
    public function delete(Articles $article, EntityManagerInterface $em): Response
    {
        $em->remove($article); // Supprime l'article
        $em->flush(); // Enregistre la suppression dans la base de données

        return $this->redirectToRoute('articles_index'); // Redirige vers la liste des articles après suppression
    }
}
