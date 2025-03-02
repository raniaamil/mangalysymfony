<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\MongoDBService;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(MongoDBService $mongoDbService): Response
    {
        $mongoDbService->insertVisit('home');
        
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
