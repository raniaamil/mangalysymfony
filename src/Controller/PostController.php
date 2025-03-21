<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Manga;
use App\Entity\Like;
use App\Repository\PostRepository;
use App\Repository\MangaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;

#[Route('/posts')]
class PostController extends AbstractController
{
    #[Route('/', name: 'post_index', methods: ['GET'])]
    public function index(PostRepository $postRepository, Request $request): Response
    {
        $posts = $postRepository->findAll();

        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/new', name: 'post_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        CsrfTokenManagerInterface $csrfTokenManager,
        MangaRepository $mangaRepository
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($request->isMethod('POST')) {
            $token = $request->request->get('_csrf_token');
            if (!$csrfTokenManager->isTokenValid(new CsrfToken('post_new', $token))) {
                throw $this->createAccessDeniedException('Token CSRF invalide.');
            }

            $post = new Post();
            $post->setTitre($request->request->get('titre'));
            $post->setContenu($request->request->get('contenu'));

            // Associer le manga via son titre
            $mangaTitre = $request->request->get('manga');
            $manga = $em->getRepository(Manga::class)->findOneBy(['titre' => $mangaTitre]);
            if ($manga) {
                $post->setManga($manga);
            } else {
                $this->addFlash('error', 'Le manga sélectionné est invalide.');
                return $this->redirectToRoute('post_new');
            }

            // Gestion minimale du média (upload d'image)
            $media = $request->files->get('media');
            if ($media) {
                $newFilename = uniqid() . '.' . $media->guessExtension();
                $media->move($this->getParameter('media_directory'), $newFilename);
                $post->setMedia($newFilename);
            }

            $post->setDatePublication(new \DateTime());
            $post->setUser($this->getUser());

            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('post_index');
        }

        $csrfToken = $csrfTokenManager->getToken('post_new')->getValue();
        $mangas = $mangaRepository->findAll();

        return $this->render('post/new.html.twig', [
            'csrf_token' => $csrfToken,
            'mangas'     => $mangas,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'post_show', methods: ['GET'])]
    public function show(Post $post, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $likeRepo = $em->getRepository(Like::class);

        // Vérifier si l'utilisateur a liké le post
        $isLiked = false;
        if ($user) {
            $isLiked = $likeRepo->findOneBy([
                'user' => $user,
                'post' => $post
            ]) ? true : false;
        }

        // Vérifier si l'utilisateur a liké les commentaires du post
        $commentaireLikes = [];
        if ($user) {
            foreach ($post->getCommentaires() as $commentaire) {
                $commentaireLikes[$commentaire->getId()] = $likeRepo->findOneBy([
                    'user' => $user,
                    'commentaire' => $commentaire
                ]) ? true : false;
            }
        }

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'isLiked' => $isLiked,
            'commentaireLikes' => $commentaireLikes,
            'type' => 'post'
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'post_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Post $post,
        EntityManagerInterface $em,
        CsrfTokenManagerInterface $csrfTokenManager,
        MangaRepository $mangaRepository
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($request->isMethod('POST')) {
            $token = $request->request->get('_csrf_token');
            if (!$csrfTokenManager->isTokenValid(new CsrfToken('post_edit', $token))) {
                throw $this->createAccessDeniedException('Token CSRF invalide.');
            }

            $post->setTitre($request->request->get('titre'));
            $post->setContenu($request->request->get('contenu'));

            // Mise à jour du manga via son titre
            $mangaTitre = $request->request->get('manga');
            $manga = $em->getRepository(Manga::class)->findOneBy(['titre' => $mangaTitre]);
            if ($manga) {
                $post->setManga($manga);
            } else {
                $this->addFlash('error', 'Le manga sélectionné est invalide.');
                return $this->redirectToRoute('post_edit', ['id' => $post->getId()]);
            }

            // Gestion minimale du média (upload facultatif d'image)
            $media = $request->files->get('media');
            if ($media) {
                $newFilename = uniqid() . '.' . $media->guessExtension();
                $media->move($this->getParameter('media_directory'), $newFilename);
                $post->setMedia($newFilename);
            }

            $em->flush();

            return $this->redirectToRoute('mes_posts');
        }

        $csrfToken = $csrfTokenManager->getToken('post_edit')->getValue();
        $mangas = $mangaRepository->findAll();

        return $this->render('post/edit.html.twig', [
            'post'       => $post,
            'csrf_token' => $csrfToken,
            'mangas'     => $mangas,
        ]);
    }

    #[Route('/{id}/delete', name: 'post_delete', methods: ['POST'])]
    public function delete(Request $request, Post $post, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
            $em->remove($post);
            $em->flush();
            $this->addFlash('success', 'Post supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Erreur lors de la suppression.');
        }

        return $this->redirectToRoute('mes_posts');
    }

    #[Route('/{id}/report', name: 'post_report', methods: ['POST'])]
    public function report(Post $post, EntityManagerInterface $em, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($post->getReport()) {
            $this->addFlash('info', 'Ce post a déjà été signalé.');
        } else {
            if ($this->isCsrfTokenValid('report' . $post->getId(), $request->request->get('_token'))) {
                $post->setReport(true);
                $em->flush();
                $this->addFlash('success', 'Le post a été signalé.');
            } else {
                $this->addFlash('error', 'Erreur lors du signalement.');
            }
        }

        return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
    }

    #[Route('/mesposts', name: 'mes_posts', methods: ['GET'])]
    public function myPosts(PostRepository $postRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $posts = $postRepository->findBy(['user' => $user]);

        return $this->render('post/mesposts.html.twig', [
            'posts' => $posts,
        ]);
    }
}
