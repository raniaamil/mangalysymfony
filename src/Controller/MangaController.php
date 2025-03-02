<?php

namespace App\Controller;

use App\Entity\Manga;
use App\Repository\MangaRepository;
use App\Repository\GenreRepository;
use App\Repository\CritiquesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;

#[Route('/mangas')]
class MangaController extends AbstractController
{
    #[Route('/', name: 'manga_index', methods: ['GET'])]
    public function index(MangaRepository $mangaRepository): Response
    {
        $mangas = $mangaRepository->findAll();

        return $this->render('manga/index.html.twig', ['mangas' => $mangas]);
    }

    #[Route('/new', name: 'manga_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, GenreRepository $genreRepository, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $genres = $genreRepository->findAll();

        if ($request->isMethod('POST')) {
            $token = $request->request->get('_csrf_token');

            if (!$csrfTokenManager->isTokenValid(new CsrfToken('manga_new', $token))) {
                throw $this->createAccessDeniedException('Token CSRF invalide.');
            }

            $manga = new Manga();
            $manga->setTitre($request->request->get('titre'));
            $manga->setAuteur($request->request->get('auteur'));
            $manga->setDateSortie(new \DateTime($request->request->get('date_sortie')));
            $manga->setDescription($request->request->get('description'));
            $manga->setImage($request->request->get('image'));
            $genreId = $request->request->get('genre_id');
            $genre = $genreRepository->find($genreId);

            if (!$genre) {
                throw $this->createNotFoundException('Le genre sélectionné est introuvable.');
            }

            $manga->setGenre($genre);
            $em->persist($manga);
            $em->flush();

            return $this->redirectToRoute('manga_index');
        }

        $csrfToken = $csrfTokenManager->getToken('manga_new')->getValue();

        return $this->render('manga/new.html.twig', [
            'genres' => $genres,
            'csrf_token' => $csrfToken,
        ]);
    }

    #[Route('/{id}/edit', name: 'manga_edit', methods: ['GET', 'POST'])]
    public function edit(Manga $manga, Request $request, EntityManagerInterface $em, GenreRepository $genreRepository, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $genres = $genreRepository->findAll();

        if ($request->isMethod('POST')) {
            $token = $request->request->get('_csrf_token');

            if (!$csrfTokenManager->isTokenValid(new CsrfToken('manga_edit', $token))) {
                throw $this->createAccessDeniedException('Token CSRF invalide.');
            }

            $manga->setTitre($request->request->get('titre'));
            $manga->setAuteur($request->request->get('auteur'));
            $manga->setDateSortie(new \DateTime($request->request->get('date_sortie')));
            $manga->setDescription($request->request->get('description'));
            $manga->setImage($request->request->get('image'));
            $genreId = $request->request->get('genre_id');
            $genre = $genreRepository->find($genreId);

            if (!$genre) {
                throw $this->createNotFoundException('Le genre sélectionné est introuvable.');
            }
            $manga->setGenre($genre);

            $em->flush();

            return $this->redirectToRoute('manga_index');
        }

        $csrfToken = $csrfTokenManager->getToken('manga_edit')->getValue();

        return $this->render('manga/edit.html.twig', [
            'manga' => $manga,
            'genres' => $genres,
            'csrf_token' => $csrfToken,
        ]);
    }

    #[Route('/{id}/delete', name: 'manga_delete', methods: ['POST'])]
    public function delete(Manga $manga, EntityManagerInterface $em, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete' . $manga->getId(), $request->request->get('_token'))) {
            $em->remove($manga);
            $em->flush();
            $this->addFlash('success', 'Manga supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Erreur lors de la suppression.');
        }
        return $this->redirectToRoute('manga_index');
    }

    #[Route('/{id}', name: 'manga_show', methods: ['GET'])]
    public function show(MangaRepository $mangaRepository, Manga $manga, CritiquesRepository $critiquesRepository): Response
    {
        $critiques = $critiquesRepository->findBy(['manga' => $manga]);

        return $this->render('manga/show.html.twig', ['manga' => $manga, 'critiques' => $critiques]);
    }
}
