<?php

namespace App\Controller;

use App\Entity\Genre; 
use App\Repository\GenreRepository; 
use Doctrine\ORM\EntityManagerInterface; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController; 
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response; 
use Symfony\Component\Routing\Annotation\Route; 
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdminGenreController extends AbstractController
{
    #[Route('/admin/genres', name: 'genre_index', methods: ['GET'])] 
    public function index(GenreRepository $genreRepository): Response
    {
        $genre = $genreRepository->findAll(); 

        return $this->render('admin/genres/index.html.twig', ['genre' => $genre]); 
    }

    #[Route('/admin/genres/new', name: 'genre_new', methods: ['GET', 'POST'])] 
    public function new(Request $request, EntityManagerInterface $em, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
    
        if ($request->isMethod('POST')) {
            $submittedToken = $request->request->get('_token');
    
            if (!$csrfTokenManager->isTokenValid(new CsrfToken('new_genre', $submittedToken))) {
                throw $this->createAccessDeniedException('Token CSRF invalide');
            }
    
            $genre = new Genre();
            $genre->setNom($request->request->get('nom'));
    
            $em->persist($genre);
            $em->flush();
    
            return $this->redirectToRoute('genre_index');
        }
    
        return $this->render('admin/genres/new.html.twig');
    }    

    #[Route('/admin/genres/{id}/edit', name: 'genre_edit', methods: ['GET', 'POST'])] 
    public function edit(Genre $genre, Request $request, EntityManagerInterface $em, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
    
        if ($request->isMethod('POST')) {
            $submittedToken = $request->request->get('_token');
    
            if (!$csrfTokenManager->isTokenValid(new CsrfToken('edit_genre', $submittedToken))) {
                throw $this->createAccessDeniedException('Token CSRF invalide');
            }
    
            $genre->setNom($request->request->get('nom'));
            $em->flush();
    
            return $this->redirectToRoute('genre_index');
        }
    
        return $this->render('admin/genres/edit.html.twig', ['genre' => $genre]);
    }

    #[Route('/admin/genres/{id}/delete', name: 'genre_delete', methods: ['POST'])] 
    public function delete(Genre $genre, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $em->remove($genre); 
        $em->flush(); 

        return $this->redirectToRoute('genre_index'); 
    }

    // ------------------------------------------  API - Endpoints  -------------------------------- //

    #[Route('/api/genres', name: 'api_genre_index', methods: ['GET'])]
    public function apiIndex(GenreRepository $genreRepository): JsonResponse
    {
        $genres = $genreRepository->findAll();
        
        $data = [];
        foreach ($genres as $genre) {
            $data[] = [
                'id' => $genre->getId(),
                'nom' => $genre->getNom()
            ];
        }
        
        return new JsonResponse($data);
    }

    #[Route('/api/genres/{id}', name: 'api_genre_show', methods: ['GET'])]
    public function apiShow(Genre $genre): JsonResponse
    {
        return new JsonResponse([
            'id' => $genre->getId(),
            'nom' => $genre->getNom()
        ]);
    }

    #[Route('/api/genres', name: 'api_genre_create', methods: ['POST'])]
    public function apiCreate(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['nom']) || empty($data['nom'])) {
            return new JsonResponse(['error' => 'Le nom est requis'], Response::HTTP_BAD_REQUEST);
        }
        
        $genre = new Genre();
        $genre->setNom($data['nom']);
        
        $em->persist($genre);
        $em->flush();
        
        return new JsonResponse([
            'id' => $genre->getId(),
            'nom' => $genre->getNom()
        ], Response::HTTP_CREATED);
    }

    #[Route('/api/genres/{id}', name: 'api_genre_update', methods: ['PUT'])]
    public function apiUpdate(Genre $genre, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['nom']) || empty($data['nom'])) {
            return new JsonResponse(['error' => 'Le nom est requis'], Response::HTTP_BAD_REQUEST);
        }
        
        $genre->setNom($data['nom']);
        $em->flush();
        
        return new JsonResponse([
            'id' => $genre->getId(),
            'nom' => $genre->getNom()
        ]);
    }

    #[Route('/api/genres/{id}', name: 'api_genre_delete', methods: ['DELETE'])]
    public function apiDelete(Genre $genre, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $em->remove($genre);
        $em->flush();
        
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}