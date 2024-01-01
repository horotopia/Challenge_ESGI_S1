<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
#[IsGranted('ROLE_COMPTABLE')]
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
