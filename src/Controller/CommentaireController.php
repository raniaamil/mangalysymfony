<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Critiques;
use App\Entity\Theorie;
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

    // 2. Ajouter un nouveau commentaire pour 'Critiques' ou 'Theorie'
    #[Route('/new/{entity}/{id}', name: 'commentaire_new', methods: ['POST'])]
    public function new(
        string $entity,
        int $id,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        // Vérifier si l'utilisateur est connecté
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Identifier l'entité cible (Critiques ou Theorie)
        $targetEntity = match ($entity) {
            'theorie' => Theorie::class,
            'critiques' => Critiques::class,
            default => null,
        };

        if (!$targetEntity) {
            throw $this->createNotFoundException("Entité invalide.");
        }

        // Récupérer l'entité cible
        $target = $em->getRepository($targetEntity)->find($id);
        if (!$target) {
            throw $this->createNotFoundException("L'entité demandée n'existe pas.");
        }

        // Créer le commentaire
        $contenu = $request->request->get('contenu');
        if (!$contenu) {
            return $this->redirectToRoute($entity . '_show', ['id' => $id]);
        }

        $commentaire = new Commentaire();
        $commentaire->setContenu($contenu);
        $commentaire->setDatePublication(new \DateTime());
        $commentaire->setUser($this->getUser());

        // Associer le commentaire à l'entité cible
        if ($entity === 'theorie') {
            $commentaire->setTheorie($target);
        } elseif ($entity === 'critiques') {
            $commentaire->setCritiques($target);
        }

        $em->persist($commentaire);
        $em->flush();

        // Redirection vers la vue de l'entité
        return $this->redirectToRoute($entity . '_show', ['id' => $id]);
    }

    // 3. Modifier un commentaire
    #[Route('/{id}/edit', name: 'commentaire_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Commentaire $commentaire,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($request->isMethod('POST')) {
            $commentaire->setContenu($request->request->get('contenu'));
            $em->flush();

            // Redirige selon l'entité liée
            $entity = $commentaire->getTheorie() ? 'theorie' : 'critiques';
            $targetId = $entity === 'theorie'
                ? $commentaire->getTheorie()->getId()
                : $commentaire->getCritiques()->getId();


            return $this->redirectToRoute($entity . '_show', ['id' => $targetId]);
        }

        return $this->render('commentaire/edit.html.twig', [
            'commentaire' => $commentaire,
        ]);
    }

    // 4. Supprimer un commentaire
    #[Route('/{id}/delete', name: 'commentaire_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Commentaire $commentaire,
        EntityManagerInterface $em
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($this->isCsrfTokenValid('delete' . $commentaire->getId(), $request->request->get('_token'))) {
            $entity = $commentaire->getTheorie() ? 'theorie' : 'critiques';
            $targetId = $entity === 'theorie'
                ? $commentaire->getTheorie()->getId()
                : $commentaire->getCritiques()->getId();

            $em->remove($commentaire);
            $em->flush();

            // Redirige vers la vue de l'entité liée
            return $this->redirectToRoute($entity . '_show', ['id' => $targetId]);
        }

        throw $this->createAccessDeniedException('Action non autorisée.');
    }
}
