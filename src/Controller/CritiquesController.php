<?php

namespace App\Controller;

use App\Entity\Critiques; // On importe la classe Critiques pour manipuler l'entité
use App\Repository\CritiquesRepository; // On importe le repository pour interagir avec la base de données
use Doctrine\ORM\EntityManagerInterface; // Pour interagir avec la base de données
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; // Base des contrôleurs Symfony
use Symfony\Component\HttpFoundation\Request; // Pour gérer les requêtes HTTP
use Symfony\Component\HttpFoundation\Response; // Pour renvoyer des réponses HTTP
use Symfony\Component\Routing\Annotation\Route; // Pour définir les routes

#[Route('/critiques')] // Route de base pour le contrôleur Critiques
class CritiquesController extends AbstractController
{
    
    #[Route('/', name: 'critiques_index', methods: ['GET'])]
    public function index(CritiquesRepository $critiquesRepository, Request $request): Response
    {
        $genre = $request->query->get('genre'); // Récupère la catégorie sélectionnée
    
        if ($genre) {
            // Filtre les critiques par catégorie
            $critiques = $critiquesRepository->findBy(['genre' => $genre]);
        } else {
            // Récupère toutes les critiques
            $critiques = $critiquesRepository->findAll();
        }
    
        return $this->render('critiques/index.html.twig', [
            'critiques' => $critiques,
        ]);
    }
    
    #[Route('/new', name: 'critiques_new', methods: ['GET', 'POST'])] // Route pour créer une nouvelle critique
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); 
        
        if ($request->isMethod('POST')) { // Vérifie si la requête est POST
            $critique = new Critiques(); // Crée une nouvelle instance de l'entité Critiques
            // Récupère les données du formulaire et les attribue à la critique
            $critique->setTitre($request->request->get('titre')); // Attribue le titre
            $critique->setContenu($request->request->get('contenu')); // Attribue le contenu
            $critique->setGenre($request->request->get('genre')); // Attribue le genre

            // Associer l'utilisateur connecté
            $critique->setUser($this->getUser());

            $em->persist($critique); // Prépare la critique à être sauvegardée
            $em->flush(); // Sauvegarde dans la base de données

            return $this->redirectToRoute('critiques_index'); // Redirige vers la liste des critiques après création
        }

        return $this->render('critiques/new.html.twig'); // Affiche le formulaire de création
    }

    #[Route('/{id<\d+>}', name: 'critiques_show', methods: ['GET'])]
    public function show(Critiques $critique): Response
    {
        return $this->render('critiques/show.html.twig', [
            'critique' => $critique,
        ]);
    }

    #[Route('/{id}/edit', name: 'critiques_edit', methods: ['GET', 'POST'])] // Route pour modifier une critique existante
    public function edit(Critiques $critique, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); 

        if ($request->isMethod('POST')) { // Vérifie si la requête est POST
            // Met à jour les informations de la critique
            $critique->setTitre($request->request->get('titre')); // Modifie le titre
            $critique->setContenu($request->request->get('contenu')); // Modifie le contenu
            $critique->setGenre($request->request->get('genre')); // Modifie le genre

            $em->flush(); // Sauvegarde les modifications

            return $this->redirectToRoute('critiques_index'); // Redirige vers la liste des critiques
        }

        return $this->render('critiques/edit.html.twig', ['critique' => $critique]); // Affiche le formulaire de modification
    }

    #[Route('/{id}/delete', name: 'critiques_delete', methods: ['POST'])] // Route pour supprimer une critique
    public function delete(Critiques $critique, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); 

        $em->remove($critique); // Supprime la critique
        $em->flush(); // Enregistre la suppression dans la base de données

        return $this->redirectToRoute('critiques_index'); // Redirige vers la liste des critiques après suppression
    }
}
