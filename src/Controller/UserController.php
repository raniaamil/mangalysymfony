<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Csrf\CsrfToken;

#[Route('/users')]
class UserController extends AbstractController
{
    #[Route('/', name: 'user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();

        return $this->render('user/index.html.twig', ['users' => $users]);
    }

    #[Route('/new', name: 'user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        if ($request->isMethod('POST')) {
            $token = $request->request->get('_csrf_token');

            if (!$csrfTokenManager->isTokenValid(new CsrfToken('user_new', $token))) {
                throw $this->createAccessDeniedException('Token CSRF invalide.');
            }

            $user = new User();
            $user->setUsername($request->request->get('username'));
            $user->setEmail($request->request->get('email'));
            $user->setDateInscription(new \DateTime());

            $hashedPassword = password_hash($request->request->get('password'), PASSWORD_BCRYPT);
            $user->setPassword($hashedPassword);

            $role = $request->request->get('role', 'ROLE_USER');
            $user->setRoles([$role]);

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('user_index');
        }

        $csrfToken = $csrfTokenManager->getToken('user_new')->getValue();

        return $this->render('user/new.html.twig', [
            'csrf_token' => $csrfToken,
        ]);
    }

    #[Route('/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(User $user, Request $request, EntityManagerInterface $em, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        if ($request->isMethod('POST')) {
            $token = $request->request->get('_csrf_token');

            if (!$csrfTokenManager->isTokenValid(new CsrfToken('user_edit', $token))) {
                throw $this->createAccessDeniedException('Token CSRF invalide.');
            }

            $user->setUsername($request->request->get('username'));
            $user->setEmail($request->request->get('email'));

            $role = $request->request->get('role', 'ROLE_USER');
            $user->setRoles([$role]);

            $em->flush();

            return $this->redirectToRoute('user_index');
        }

        $csrfToken = $csrfTokenManager->getToken('user_edit')->getValue();

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'csrf_token' => $csrfToken,
        ]);
    }

    #[Route('/{id}/delete', name: 'user_delete', methods: ['POST'])]
    public function delete(User $user, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $em->remove($user);
            $em->flush();
        }

        return $this->redirectToRoute('user_index');
    }
}
