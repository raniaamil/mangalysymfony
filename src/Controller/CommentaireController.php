<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Critiques;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/commentaire')]
class CommentaireController extends AbstractController
{
    // 1. Liste des commentaires
    #[Route('/', name: 'commentaire_index', methods: ['GET'])]
    public function index(CommentaireRepository $commentaireRepository): Response
    {
        $commentaires = $commentaireRepository->findAll();

        return $this->render('commentaire/index.html.twig', [
            'commentaires' => $commentaires,
        ]);
    }

    // 2. Ajouter un nouveau commentaire
    #[Route('/new/{id<\d+>}', name: 'commentaire_new', methods: ['GET', 'POST'])]
    public function new(Request $request, Critiques $critique, EntityManagerInterface $em): Response
    {
        // Vérifie si l'utilisateur est connecté
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($request->isMethod('POST')) {
            $commentaire = new Commentaire();
            $commentaire->setContenu($request->request->get('contenu')); // Récupère le contenu
            $commentaire->setDatePublication(new \DateTime()); // Date actuelle
            $commentaire->setCritiques($critique); // Lie le commentaire à la critique
            $commentaire->setUser($this->getUser()); // Associe l'utilisateur connecté

            $em->persist($commentaire);
            $em->flush();

            return $this->redirectToRoute('critiques_show', ['id' => $critique->getId()]);
        }

        return $this->render('commentaire/new.html.twig', [
            'critique' => $critique,
        ]);
    }

    // 3. Modifier un commentaire
    #[Route('/{id<\d+>}/edit', name: 'commentaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commentaire $commentaire, EntityManagerInterface $em): Response
    {
        // Vérifie si l'utilisateur est connecté
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($request->isMethod('POST')) {
            $commentaire->setContenu($request->request->get('contenu'));
            $em->flush();

            return $this->redirectToRoute('commentaire_index');
        }

        return $this->render('commentaire/edit.html.twig', [
            'commentaire' => $commentaire,
        ]);
    }

    // 4. Supprimer un commentaire
    #[Route('/{id<\d+>}/delete', name: 'commentaire_delete', methods: ['POST'])]
    public function delete(Request $request, Commentaire $commentaire, EntityManagerInterface $em): Response
    {
        // Vérifie si l'utilisateur est connecté
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($this->isCsrfTokenValid('delete' . $commentaire->getId(), $request->request->get('_token'))) {
            $em->remove($commentaire);
            $em->flush();
        }

        return $this->redirectToRoute('commentaire_index');
    }
}
