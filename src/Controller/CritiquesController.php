<?php

namespace App\Controller;

use App\Entity\Critiques;
use App\Entity\Manga;
use App\Entity\Like;
use App\Repository\CritiquesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/critiques')]
class CritiquesController extends AbstractController
{
    #[Route('/', name: 'critiques_index', methods: ['GET'])]
    public function index(CritiquesRepository $critiquesRepository, Request $request): Response
    {
        $genre = $request->query->get('genre');

        if ($genre) {
            $critiques = $critiquesRepository->findBy(['genre' => $genre]);
        } else {
            $critiques = $critiquesRepository->findAll();
        }

        return $this->render('critiques/index.html.twig', [
            'critiques' => $critiques,
        ]);
    }

    #[Route('/new', name: 'critiques_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($request->isMethod('POST')) {
            $critique = new Critiques();
            $critique->setTitre($request->request->get('titre'));
            $critique->setContenu($request->request->get('contenu'));

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

            $em->persist($critique);

            $noteValeur = (int)$request->request->get('note');
            $note = new \App\Entity\Note();
            $note->setValeur($noteValeur);
            $note->setUser($this->getUser());
            $note->setManga($manga);
            $note->setCritique($critique);

            $em->persist($note);
            $em->flush();

            return $this->redirectToRoute('critiques_index');
        }

        return $this->render('critiques/new.html.twig');
    }

    #[Route('/{id<\d+>}', name: 'critiques_show', methods: ['GET'])]
    public function show(Critiques $critique, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $likeRepo = $em->getRepository(Like::class);

        $isLiked = false;
        if ($user) {
            $isLiked = $likeRepo->findOneBy([
                'user' => $user,
                'critiques' => $critique
            ]) ? true : false;
        }

        return $this->render('critiques/show.html.twig', [
            'critique' => $critique,
            'isLiked' => $isLiked,
            'type' => 'critiques'
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'critiques_edit', methods: ['GET', 'POST'])]
    public function edit(Critiques $critique, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if ($request->isMethod('POST')) {
            $critique->setTitre($request->request->get('titre'));
            $critique->setContenu($request->request->get('contenu'));

            $mangaTitre = $request->request->get('manga');
            $manga = $em->getRepository(Manga::class)->findOneBy(['titre' => $mangaTitre]);

            if ($manga) {
                $critique->setManga($manga);
            } else {
                $this->addFlash('error', 'Le manga sélectionné est invalide.');
                return $this->redirectToRoute('critiques_edit', ['id' => $critique->getId()]);
            }

            $em->flush();

            return $this->redirectToRoute('critiques_index');
        }

        return $this->render('critiques/edit.html.twig', ['critique' => $critique]);
    }

    #[Route('/{id}/delete', name: 'critiques_delete', methods: ['POST'])]
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
        return $this->redirectToRoute('critiques_index');
    }

    #[Route('/{id}/report', name: 'critiques_report', methods: ['POST'])]
    public function report(Critiques $critique, EntityManagerInterface $em, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

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

    #[Route('/page1', name: 'critiques_page1', methods: ['GET'])]
    public function page1(): Response
    {
        return $this->render('critiques/page1.html.twig');
    }

    #[Route('/page2', name: 'critiques_page2', methods: ['GET'])]
    public function page2(): Response
    {
        return $this->render('critiques/page2.html.twig');
    }

    #[Route('/page3', name: 'critiques_page3', methods: ['GET'])]
    public function page3(): Response
    {
        return $this->render('critiques/page3.html.twig');
    }

}
