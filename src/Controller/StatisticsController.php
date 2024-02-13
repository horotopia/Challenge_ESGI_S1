<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
#[IsGranted('ROLE_COMPTABLE')]
class StatisticsController extends AbstractController
{
    #[Route('/admin/statistics', name: 'app_back_statistics')]
    public function index(): Response
    {
        return $this->render('back/statistics/index.html.twig', [
            'controller_name' => 'StatisticsController',
        ]);
    }
}
