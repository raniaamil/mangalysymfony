<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NoteContollerController extends AbstractController
{
    #[Route('/note/contoller', name: 'app_note_contoller')]
    public function index(): Response
    {
        return $this->render('note_contoller/index.html.twig', [
            'controller_name' => 'NoteContollerController',
        ]);
    }
}
