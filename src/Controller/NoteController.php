<?php

namespace App\Controller;

use App\Entity\Note;
use App\Entity\Critiques;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/notes')]
class NoteController extends AbstractController
{
    #[Route('/new/{critiqueId}', name: 'note_new', methods: ['POST'])]
    public function new(
        int $critiqueId,
        Request $request,
        EntityManagerInterface $em,
        NoteRepository $noteRepository
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $user = $this->getUser();
        $notation = (int) $request->request->get('notation');

        if ($notation < 1 || $notation > 5) {
            return $this->json(['error' => 'La note doit être comprise entre 1 et 5.'], 400);
        }

        // Vérifie si l'utilisateur a déjà noté cette critique
        $existingNote = $noteRepository->findOneBy(['user' => $user, 'critique' => $critiqueId]);
        if ($existingNote) {
            return $this->json(['error' => 'Vous avez déjà noté cette critique.'], 400);
        }

        $critique = $em->getRepository(Critiques::class)->find($critiqueId);
        if (!$critique) {
            return $this->json(['error' => 'Critique introuvable.'], 404);
        }

        $note = new Note();
        $note->setUser($user);
        $note->setCritique($critique);
        $note->setNotation($notation);

        $em->persist($note);
        $em->flush();

        return $this->json(['success' => 'Note ajoutée avec succès.']);
    }

    #[Route('/edit/{id}', name: 'note_edit', methods: ['POST'])]
    public function edit(
        int $id,
        Request $request,
        EntityManagerInterface $em,
        NoteRepository $noteRepository
    ): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $note = $noteRepository->find($id);

        if (!$note) {
            return $this->json(['error' => 'Note introuvable.'], 404);
        }

        if ($note->getUser() !== $this->getUser()) {
            return $this->json(['error' => 'Vous n\'avez pas le droit de modifier cette note.'], 403);
        }

        $notation = (int) $request->request->get('notation');
        if ($notation < 1 || $notation > 5) {
            return $this->json(['error' => 'La note doit être comprise entre 1 et 5.'], 400);
        }

        $note->setNotation($notation);

        $em->flush();

        return $this->json(['success' => 'Note modifiée avec succès.']);
    }
}
