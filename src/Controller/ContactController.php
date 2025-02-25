<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact', methods: ['GET', 'POST'])]
    public function index(Request $request, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        if ($request->isMethod('POST')) {
            $token = $request->request->get('_csrf_token');

            if (!$csrfTokenManager->isTokenValid(new CsrfToken('contact_form', $token))) {
                throw $this->createAccessDeniedException('Token CSRF invalide.');
            }

            // Traitez les données du formulaire ici
            $username = $request->request->get('username');
            $email = $request->request->get('email');
            $subject = $request->request->get('subject');
            $message = $request->request->get('message');

            // Ajoutez ici le code pour envoyer l'email ou enregistrer les données

            $this->addFlash('success', 'Votre message a été envoyé avec succès.');

            return $this->redirectToRoute('app_contact');
        }

        $csrfToken = $csrfTokenManager->getToken('contact_form')->getValue();

        return $this->render('contact/index.html.twig', [
            'csrf_token' => $csrfToken,
        ]);
    }
}
