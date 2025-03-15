<?php

namespace App\Controller;

use App\Entity\Theorie;
use App\Entity\Manga;
use App\Entity\Like;
use App\Repository\TheorieRepository;
use App\Repository\MangaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;

#[Route('/theories')]
class TheorieController extends AbstractController
{
    #[Route('/', name: 'theorie_index', methods: ['GET'])]
    public function index(TheorieRepository $theorieRepository, Request $request): Response
    {
        $theories = $theorieRepository->findAll();

        return $this->render('theorie/index.html.twig', [
            'theories' => $theories,
        ]);
    }

    #[Route('/new', name: 'theorie_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        CsrfTokenManagerInterface $csrfTokenManager,
        MangaRepository $mangaRepository
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($request->isMethod('POST')) {
            $token = $request->request->get('_csrf_token');
            if (!$csrfTokenManager->isTokenValid(new CsrfToken('theorie_new', $token))) {
                throw $this->createAccessDeniedException('Token CSRF invalide.');
            }

            $theorie = new Theorie();
            $theorie->setTitre($request->request->get('titre'));
            $theorie->setContenu($request->request->get('contenu'));

            // Associer le manga via son titre
            $mangaTitre = $request->request->get('manga');
            $manga = $em->getRepository(Manga::class)->findOneBy(['titre' => $mangaTitre]);
            if ($manga) {
                $theorie->setManga($manga);
            } else {
                $this->addFlash('error', 'Le manga sélectionné est invalide.');
                return $this->redirectToRoute('theorie_new');
            }

            // Gestion minimale du média (upload d'image)
            $media = $request->files->get('media');
            if ($media) {
                $newFilename = uniqid() . '.' . $media->guessExtension();
                $media->move($this->getParameter('media_directory'), $newFilename);
                $theorie->setMedia($newFilename);
            }

            $theorie->setDatePublication(new \DateTime());
            $theorie->setUser($this->getUser());

            $em->persist($theorie);
            $em->flush();

            return $this->redirectToRoute('theorie_index');
        }

        $csrfToken = $csrfTokenManager->getToken('theorie_new')->getValue();

        // Si vous souhaitez afficher la liste des mangas dans le formulaire :
        $mangas = $mangaRepository->findAll();

        return $this->render('theorie/new.html.twig', [
            'csrf_token' => $csrfToken,
            'mangas'     => $mangas,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'theorie_show', methods: ['GET'])]
    public function show(Theorie $theorie, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $likeRepo = $em->getRepository(Like::class);

        // Vérifier si l'utilisateur a liké la théorie
        $isLiked = false;
        if ($user) {
            $isLiked = $likeRepo->findOneBy([
                'user' => $user,
                'theorie' => $theorie
            ]) ? true : false;
        }

        // Vérifier si l'utilisateur a liké les commentaires
        $commentaireLikes = [];
        if ($user) {
            foreach ($theorie->getCommentaires() as $commentaire) {
                $commentaireLikes[$commentaire->getId()] = $likeRepo->findOneBy([
                    'user' => $user,
                    'commentaire' => $commentaire
                ]) ? true : false;
            }
        }

        // On ne fait plus de conversion base64 ; le template affichera l'image via asset()
        return $this->render('theorie/show.html.twig', [
            'theorie' => $theorie,
            'isLiked' => $isLiked,
            'commentaireLikes' => $commentaireLikes,
            'type' => 'theorie'
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'theorie_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Theorie $theorie,
        EntityManagerInterface $em,
        CsrfTokenManagerInterface $csrfTokenManager,
        MangaRepository $mangaRepository
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($request->isMethod('POST')) {
            $token = $request->request->get('_csrf_token');
            if (!$csrfTokenManager->isTokenValid(new CsrfToken('theorie_edit', $token))) {
                throw $this->createAccessDeniedException('Token CSRF invalide.');
            }

            $theorie->setTitre($request->request->get('titre'));
            $theorie->setContenu($request->request->get('contenu'));

            // Mise à jour du manga via son titre
            $mangaTitre = $request->request->get('manga');
            $manga = $em->getRepository(Manga::class)->findOneBy(['titre' => $mangaTitre]);
            if ($manga) {
                $theorie->setManga($manga);
            } else {
                $this->addFlash('error', 'Le manga sélectionné est invalide.');
                return $this->redirectToRoute('theorie_edit', ['id' => $theorie->getId()]);
            }

            // Gestion minimale du média (upload facultatif d'image)
            $media = $request->files->get('media');
            if ($media) {
                $newFilename = uniqid() . '.' . $media->guessExtension();
                $media->move($this->getParameter('media_directory'), $newFilename);
                $theorie->setMedia($newFilename);
            }

            $em->flush();

            return $this->redirectToRoute('mes_theories');
        }

        $csrfToken = $csrfTokenManager->getToken('theorie_edit')->getValue();

        return $this->render('theorie/edit.html.twig', [
            'theorie'    => $theorie,
            'csrf_token' => $csrfToken,
        ]);
    }

    #[Route('/{id}/delete', name: 'theorie_delete', methods: ['POST'])]
    public function delete(Request $request, Theorie $theorie, EntityManagerInterface $em): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($this->isCsrfTokenValid('delete' . $theorie->getId(), $request->request->get('_token'))) {
            $em->remove($theorie);
            $em->flush();
        }

        return $this->redirectToRoute('mes_theories');
    }

    #[Route('/{id}/report', name: 'theorie_report', methods: ['POST'])]
    public function report(Theorie $theorie, EntityManagerInterface $em, Request $request): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($this->isCsrfTokenValid('report' . $theorie->getId(), $request->request->get('_token'))) {
            $theorie->setReport(true);
            $em->flush();
            $this->addFlash('success', 'La théorie a été signalée.');
        } else {
            $this->addFlash('error', 'Erreur lors du signalement.');
        }

        return $this->redirectToRoute('theorie_show', ['id' => $theorie->getId()]);
    }

    #[Route('/mestheories', name: 'mes_theories', methods: ['GET'])]
    public function myTheories(TheorieRepository $theorieRepository): Response {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }
        $theories = $theorieRepository->findBy(['user' => $user]);

        return $this->render('theorie/mes_theories.html.twig', [
            'theories' => $theories,
        ]);
    }
}

