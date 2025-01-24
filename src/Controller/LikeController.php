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

#[Route('/likes')]
class LikeController extends AbstractController
{
    #[Route('/add', name: 'like_add', methods: ['POST'])]
    public function addLike(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['message' => 'Authentication required.'], Response::HTTP_FORBIDDEN);
        }

        $entityId = $request->request->get('id');
        $entityType = $request->request->get('type'); // 'theorie', 'critique', 'post', 'commentaire'

        $like = new Like();
        $like->setUser($user);

        switch ($entityType) {
            case 'theorie':
                $entity = $em->getRepository(Theorie::class)->find($entityId);
                if (!$entity) {
                    return $this->json(['message' => 'Théorie non trouvée.'], Response::HTTP_NOT_FOUND);
                }
                $like->setTheorie($entity);
                break;

            case 'critique':
                $entity = $em->getRepository(Critiques::class)->find($entityId);
                if (!$entity) {
                    return $this->json(['message' => 'Critique non trouvée.'], Response::HTTP_NOT_FOUND);
                }
                $like->setCritique($entity);
                break;

            case 'post':
                $entity = $em->getRepository(Post::class)->find($entityId);
                if (!$entity) {
                    return $this->json(['message' => 'Post non trouvé.'], Response::HTTP_NOT_FOUND);
                }
                $like->setPost($entity);
                break;

            case 'commentaire':
                $entity = $em->getRepository(Commentaire::class)->find($entityId);
                if (!$entity) {
                    return $this->json(['message' => 'Commentaire non trouvé.'], Response::HTTP_NOT_FOUND);
                }
                $like->setCommentaire($entity);
                break;

            default:
                return $this->json(['message' => 'Type non valide.'], Response::HTTP_BAD_REQUEST);
        }

        $em->persist($like);
        $em->flush();

        return $this->json(['message' => 'Like ajouté avec succès !'], Response::HTTP_CREATED);
    }

    #[Route('/remove', name: 'like_remove', methods: ['POST'])]
    public function removeLike(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json(['message' => 'Authentication required.'], Response::HTTP_FORBIDDEN);
        }

        $entityId = $request->request->get('id');
        $entityType = $request->request->get('type');

        $likeRepo = $em->getRepository(Like::class);

        $like = null;

        switch ($entityType) {
            case 'theorie':
                $like = $likeRepo->findOneBy(['theorie' => $entityId, 'user' => $user]);
                break;

            case 'critique':
                $like = $likeRepo->findOneBy(['critique' => $entityId, 'user' => $user]);
                break;

            case 'post':
                $like = $likeRepo->findOneBy(['post' => $entityId, 'user' => $user]);
                break;

            case 'commentaire':
                $like = $likeRepo->findOneBy(['commentaire' => $entityId, 'user' => $user]);
                break;

            default:
                return $this->json(['message' => 'Type non valide.'], Response::HTTP_BAD_REQUEST);
        }

        if (!$like) {
            return $this->json(['message' => 'Like non trouvé.'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($like);
        $em->flush();

        return $this->json(['message' => 'Like supprimé avec succès.'], Response::HTTP_OK);
    }
}
