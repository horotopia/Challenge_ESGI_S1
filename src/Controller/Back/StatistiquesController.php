<?php

namespace App\Controller\Back;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatistiquesController extends AbstractController
{
    #[Route('/admin/statistiques', name: 'back_app_statistiques')]
    public function index(): Response
    {
        return $this->render('back/statistiques/index.html.twig', [
            'controller_name' => 'StatistiquesController',
        ]);
    }
}
