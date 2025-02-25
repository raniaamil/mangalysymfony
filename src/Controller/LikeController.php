<?php

namespace App\Controller;

use App\Entity\Like;
use App\Entity\Theorie;
use App\Entity\Post;
use App\Entity\Critiques;
use App\Entity\Commentaire;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;

#[Route('/likes')]
class LikeController extends AbstractController
{
    #[Route('/toggle', name: 'like_toggle', methods: ['POST'])]
    public function toggleLike(Request $request, EntityManagerInterface $em, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['message' => 'Authentication required.'], Response::HTTP_FORBIDDEN);
        }

        $token = $request->request->get('_csrf_token');

        if (!$csrfTokenManager->isTokenValid(new CsrfToken('like_toggle', $token))) {
            return $this->json(['message' => 'Token CSRF invalide.'], Response::HTTP_FORBIDDEN);
        }

        $entityId = $request->request->get('id');
        $entityType = $request->request->get('type');

        $likeRepo = $em->getRepository(Like::class);

        $like = null;
        $entity = null;
        $isLiked = false;
        $type = '';

        switch ($entityType) {
            case 'theorie':
                $entity = $em->getRepository(Theorie::class)->find($entityId);
                $like = $likeRepo->findOneBy(['theorie' => $entity, 'user' => $user]);
                $isLiked = $like ? true : false;
                $type = 'theorie';
                break;

            case 'critique':
                $entity = $em->getRepository(Critiques::class)->find($entityId);
                $like = $likeRepo->findOneBy(['critiques' => $entity, 'user' => $user]);
                $isLiked = $like ? true : false;
                $type = 'critique';
                break;

            case 'post':
                $entity = $em->getRepository(Post::class)->find($entityId);
                $like = $likeRepo->findOneBy(['post' => $entity, 'user' => $user]);
                $isLiked = $like ? true : false;
                $type = 'post';
                break;

            case 'commentaire':
                $entity = $em->getRepository(Commentaire::class)->find($entityId);
                $like = $likeRepo->findOneBy(['commentaire' => $entity, 'user' => $user]);
                $isLiked = $like ? true : false;
                $type = 'commentaire';
                break;

            default:
                return $this->json(['message' => 'Type non valide.'], Response::HTTP_BAD_REQUEST);
        }

        if (!$entity) {
            return $this->json(['message' => 'Entité non trouvée.'], Response::HTTP_NOT_FOUND);
        }

        if ($like) {
            $em->remove($like);
            $isLiked = false;
        } else {
            $like = new Like();
            $like->setUser($user);

            switch ($entityType) {
                case 'theorie':
                    $like->setTheorie($entity);
                    break;
                case 'critique':
                    $like->setCritiques($entity);
                    break;
                case 'post':
                    $like->setPost($entity);
                    break;
                case 'commentaire':
                    $like->setCommentaire($entity);
                    break;
            }

            $like->setDateCreation(new \DateTime());
            $em->persist($like);
            $isLiked = true;
        }

        $em->flush();

        return $this->json([
            'isLiked' => $isLiked,
            'type' => $type
        ], Response::HTTP_OK);
    }
}
