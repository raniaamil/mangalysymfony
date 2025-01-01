<?php

namespace App\Controller;

use App\Entity\Theorie;
use App\Entity\Manga;
use App\Repository\TheorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/theories')]
class TheorieController extends AbstractController
{
    #[Route('/', name: 'theorie_index', methods: ['GET'])]
    public function index(TheorieRepository $theorieRepository): Response
    {
        return $this->render('theorie/index.html.twig', [
            'theories' => $theorieRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'theorie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); 

        if ($request->isMethod('POST')) {
            $theorie = new Theorie();
            $theorie->setTitre($request->request->get('titre'));
            $theorie->setContenu($request->request->get('contenu'));

            // Associer le manga
            $mangaTitre = $request->request->get('manga');
            $manga = $em->getRepository(Manga::class)->findOneBy(['titre' => $mangaTitre]);

            if ($manga) {
                $theorie->setManga($manga);
            } else {
                $this->addFlash('error', 'Le manga sélectionné est invalide.');
                return $this->redirectToRoute('theorie_new');
            }

            // Gestion du média
            $media = $request->files->get('media');
            if ($media) {
                $newFilename = uniqid() . '.' . $media->guessExtension();
                $media->move(
                    $this->getParameter('media_directory'),
                    $newFilename
                );
                $theorie->setMedia($newFilename);
            }

            $theorie->setDatePublication(new \DateTime());
            $theorie->setUser($this->getUser());

            $em->persist($theorie);
            $em->flush();

            return $this->redirectToRoute('theorie_index');
        }

        return $this->render('theorie/new.html.twig');
    }

    #[Route('/{id}', name: 'theorie_show', methods: ['GET'])]
    public function show(Theorie $theorie): Response
    {
        $mediaBase64 = null; // Par défaut, pas de média.

        if ($theorie->getMedia()) { // Si un média existe.
            $mediaPath = $this->getParameter('media_directory') . '/' . $theorie->getMedia();
            if (file_exists($mediaPath)) { // Vérifie si le fichier existe.
                $mediaBase64 = base64_encode(file_get_contents($mediaPath)); // Lis et encode.
            }
        }

        return $this->render('theorie/show.html.twig', [
            'theorie' => $theorie,
            'media_base64' => $mediaBase64, // Envoie le média encodé au template.
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'theorie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Theorie $theorie, EntityManagerInterface $em): Response
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); 

        if ($request->isMethod('POST')) {
            $theorie->setTitre($request->request->get('titre'));
            $theorie->setContenu($request->request->get('contenu'));

            // Mise à jour du manga
            $mangaTitre = $request->request->get('manga');
            $manga = $em->getRepository(Manga::class)->findOneBy(['titre' => $mangaTitre]);

            if ($manga) {
                $theorie->setManga($manga);
            } else {
                $this->addFlash('error', 'Le manga sélectionné est invalide.');
                return $this->redirectToRoute('theorie_edit', ['id' => $theorie->getId()]);
            }

            // Gestion du média (facultatif)
            $media = $request->files->get('media');
            if ($media) {
                $newFilename = uniqid() . '.' . $media->guessExtension();
                $media->move(
                    $this->getParameter('media_directory'),
                    $newFilename
                );
                $theorie->setMedia($newFilename);
            }

            $em->flush();

            return $this->redirectToRoute('theorie_index');
        }

        return $this->render('theorie/edit.html.twig', [
            'theorie' => $theorie,
        ]);
    }

    #[Route('/{id}/delete', name: 'theorie_delete', methods: ['POST'])]
    public function delete(Request $request, Theorie $theorie, EntityManagerInterface $em): Response
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'); 

        if ($this->isCsrfTokenValid('delete' . $theorie->getId(), $request->request->get('_token'))) {
            $em->remove($theorie);
            $em->flush();
        }

        return $this->redirectToRoute('theorie_index');
    }
}
