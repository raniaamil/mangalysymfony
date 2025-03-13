<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Post;
use App\Entity\Theorie;
use App\Entity\Like;
use App\Repository\CommentaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;


#[Route('/commentaire')]
class CommentaireController extends AbstractController
{
    #[Route('/', name: 'commentaire_index', methods: ['GET'])]
    public function index(CommentaireRepository $commentaireRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $commentaires = $commentaireRepository->findAll();

        return $this->render('commentaire/index.html.twig', [
            'commentaires' => $commentaires,
        ]);
    }

    #[Route('/new/{entity}/{id}', name: 'commentaire_new', methods: ['GET', 'POST'])]
    public function new(string $entity, int $id, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if (!$this->isCsrfTokenValid('new_commentaire', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $targetEntity = match ($entity) {
            'theorie' => Theorie::class,
            'post' => Post::class,
            default => null,
        };

        if (!$targetEntity) {
            throw $this->createNotFoundException("Entité invalide.");
        }

        $target = $em->getRepository($targetEntity)->find($id);
        if (!$target) {
            throw $this->createNotFoundException("L'entité demandée n'existe pas.");
        }

        $contenu = $request->request->get('contenu');
        if (!$contenu) {
            return $this->redirectToRoute($entity . '_show', ['id' => $id]);
        }

        $commentaire = new Commentaire();
        $commentaire->setContenu($contenu);
        $commentaire->setDatePublication(new \DateTime());
        $commentaire->setUser($this->getUser());

        match ($entity) {
            'theorie' => $commentaire->setTheorie($target),
            'post' => $commentaire->setPost($target),
        };

        $em->persist($commentaire);
        $em->flush();

        return $this->redirectToRoute($entity . '_show', ['id' => $id]);
    }

    #[Route('/{id}/edit', name: 'commentaire_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commentaire $commentaire, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('edit_commentaire', $request->request->get('_token'))) {
                throw $this->createAccessDeniedException('Token CSRF invalide.');
            }

            $commentaire->setContenu($request->request->get('contenu'));
            $em->flush();

            $entity = $commentaire->getTheorie() ? 'theorie' : 'post';
            $targetId = $commentaire->getTheorie()?->getId() ?? $commentaire->getPost()?->getId();

            return $this->redirectToRoute($entity . '_show', ['id' => $targetId]);
        }

        return $this->render('commentaire/edit.html.twig', [
            'commentaire' => $commentaire,
        ]);
    }

    #[Route('/{id}/delete', name: 'commentaire_delete', methods: ['POST'])]
    public function delete(Request $request, Commentaire $commentaire, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($this->isCsrfTokenValid('delete' . $commentaire->getId(), $request->request->get('_token'))) {
            $entity = $commentaire->getTheorie() ? 'theorie' : 'post';
            $targetId = $commentaire->getTheorie()?->getId() ?? $commentaire->getPost()?->getId();

            $em->remove($commentaire);
            $em->flush();

            $this->addFlash('success', 'Le commentaire a bien été supprimé.');

            return $this->redirectToRoute($entity . '_show', ['id' => $targetId]);
        }

        throw $this->createAccessDeniedException('Action non autorisée.');
    }

    #[Route('/{id}/report', name: 'commentaire_report', methods: ['POST'])]
    public function report(Commentaire $commentaire, EntityManagerInterface $em, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($this->isCsrfTokenValid('report' . $commentaire->getId(), $request->request->get('_token'))) {
            $commentaire->setReport(true);
            $em->flush();

            $this->addFlash('success', 'Le commentaire a été signalé.');
        } else {
            $this->addFlash('error', 'Erreur lors du signalement.');
        }

        $parentEntity = $commentaire->getTheorie() ?: $commentaire->getPost();
        $redirectRoute = $parentEntity instanceof Theorie ? 'theorie_show' : 'post_show';

        return $this->redirectToRoute($redirectRoute, ['id' => $parentEntity->getId()]);
    }

    #[Route('/{id}/like', name: 'commentaire_like', methods: ['POST'])]
    public function toggleLike(Commentaire $commentaire, EntityManagerInterface $em, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['message' => 'Authentication required.'], Response::HTTP_FORBIDDEN);
        }
    
        if (!$this->isCsrfTokenValid('like' . $commentaire->getId(), $request->request->get('_token'))) {
            return $this->json(['message' => 'Token CSRF invalide.'], Response::HTTP_FORBIDDEN);
        }
    
        $likeRepo = $em->getRepository(Like::class);
        $existingLike = $likeRepo->findOneBy(['user' => $user, 'commentaire' => $commentaire]);
    
        if ($existingLike) {
            $em->remove($existingLike);
            $isLiked = false;
        } else {
            $like = new Like();
            $like->setUser($user);
            $like->setCommentaire($commentaire);
            $like->setDateCreation(new \DateTime());
            $em->persist($like);
            $isLiked = true;
        }
    
        $em->flush();
    
        return $this->json(['isLiked' => $isLiked]);
    }

    #[Route('/mescommentaires', name: 'mes_commentaires', methods: ['GET'])]
    public function myCommentaires(CommentaireRepository $commentaireRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }
    
        $commentaires = $commentaireRepository->findBy(['user' => $user], ['date_publication' => 'DESC']);
    
        return $this->render('commentaire/mescommentaires.html.twig', [
            'commentaires' => $commentaires,
        ]);
    }

}
