<?php

namespace App\Controller;

use App\Entity\Critiques; // On importe la classe Critiques pour manipuler l'entité
use App\Entity\Manga;
use App\Entity\Like;
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
            
            // Associer le manga
            $mangaTitre = $request->request->get('manga');
            $manga = $em->getRepository(Manga::class)->findOneBy(['titre' => $mangaTitre]);

            if ($manga) {
                $critique->setManga($manga);
            } else {
                $this->addFlash('error', 'Le manga sélectionné est invalide.');
                return $this->redirectToRoute('critiques_new');
            }
            
            $critique->setDatePublication(new \DateTime());
            $critique->setUser($this->getUser());

            $em->persist($critique); // Prépare la critique à être sauvegardée
            $em->flush(); // Sauvegarde dans la base de données

            return $this->redirectToRoute('critiques_index'); // Redirige vers la liste des critiques après création
        }

        return $this->render('critiques/new.html.twig'); // Affiche le formulaire de création
    }

    #[Route('/{id<\d+>}', name: 'critiques_show', methods: ['GET'])]
    public function show(Critiques $critiques, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $likeRepo = $em->getRepository(Like::class);
    
        // Vérifier si l'utilisateur a liké la critique
        $isLiked = false;
        if ($user) {
            $isLiked = $likeRepo->findOneBy([
                'user' => $user,
                'critiques' => $critiques
            ]) ? true : false;
        }
    
        // Vérifier si l'utilisateur a liké les commentaires
        $commentaireLikes = [];
        if ($user) {
            foreach ($critiques->getCommentaires() as $commentaire) {
                $commentaireLikes[$commentaire->getId()] = $likeRepo->findOneBy([
                    'user' => $user,
                    'commentaire' => $commentaire
                ]) ? true : false;
            }
        }
    
        return $this->render('critiques/show.html.twig', [
            'critiques' => $critiques,
            'commentaires' => $critiques->getCommentaires(),
            'isLiked' => $isLiked,
            'commentaireLikes' => $commentaireLikes,
            'type' => 'critiques'  // On passe le type pour le bouton like
        ]);
    }    

    #[Route('/{id<\d+>}/edit', name: 'critiques_edit', methods: ['GET', 'POST'])] // Route pour modifier une critique existante
    public function edit(Critiques $critique, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); 

        if ($request->isMethod('POST')) { // Vérifie si la requête est POST
            $critique->setTitre($request->request->get('titre')); // Modifie le titre
            $critique->setContenu($request->request->get('contenu')); // Modifie le contenu
            
            // Mise à jour du manga
            $mangaTitre = $request->request->get('manga');
            $manga = $em->getRepository(Manga::class)->findOneBy(['titre' => $mangaTitre]);

            if ($manga) {
                $critique->setManga($manga);
            } else {
                $this->addFlash('error', 'Le manga sélectionné est invalide.');
                return $this->redirectToRoute('critiques_edit', ['id' => $critique->getId()]);
            }

            $em->flush(); // Sauvegarde les modifications

        
            return $this->redirectToRoute('critiques_index'); // Redirige vers la liste des critiques
        }

        return $this->render('critiques/edit.html.twig', ['critique' => $critique]); // Affiche le formulaire de modification
    }

    #[Route('/{id}/delete', name: 'critiques_delete', methods: ['POST'])] // Route pour supprimer une critique
    public function delete(Critiques $critique, EntityManagerInterface $em, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); 

        if ($this->isCsrfTokenValid('delete' . $critique->getId(), $request->request->get('_token'))) {
            $em->remove($critique);
            $em->flush();
            $this->addFlash('success', 'Critique supprimée avec succès.');
        } else {
            $this->addFlash('error', 'Erreur lors de la suppression.');
        }
        return $this->redirectToRoute('critiques_index'); // Redirige vers la liste des critiques après suppression
    }

    #[Route('/{id}/report', name: 'critiques_report', methods: ['POST'])]
    public function report(Critiques $critique, EntityManagerInterface $em, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    
        // Vérifier si la critique a déjà été signalée
        if ($critique->getReport()) {
            $this->addFlash('info', 'Cette critique a déjà été signalée.');
        } else {
            if ($this->isCsrfTokenValid('report' . $critique->getId(), $request->request->get('_token'))) {
                $critique->setReport(true);
                $em->flush();
                $this->addFlash('success', 'La critique a été signalée.');
            } else {
                $this->addFlash('error', 'Erreur lors du signalement.');
            }
        }
    
        return $this->redirectToRoute('critiques_show', ['id' => $critique->getId()]);
    }

    #[Route('/mescritiques', name: 'mes_critiques', methods: ['GET'])]
    public function mesCritiques(CritiquesRepository $critiquesRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }
    
        $critiques = $critiquesRepository->findBy(['user' => $user]);
    
        return $this->render('critiques/mescritiques.html.twig', [
            'critiques' => $critiques,
        ]);
    }
    
    
}
