<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatistiquesController extends AbstractController
{
    #[Route('/admin/statistiques', name: 'app_back_statistiques')]
    public function index(): Response
    {
        return $this->render('back/statistiques/index.html.twig', [
            'controller_name' => 'StatistiquesController',
        ]);
    }
}
